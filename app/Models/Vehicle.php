<?php

namespace App\Models;

use PDO;

class Vehicle extends Model
{
    private $vehicleBrandColumnsCache = null;

    public function saveVehicle($data)
    {
        return true;
    }

    private function getVehicleBrandColumns(): array
    {
        if ($this->vehicleBrandColumnsCache !== null) {
            return $this->vehicleBrandColumnsCache;
        }

        $columns = [];

        try {
            // Ensure we query the intended database explicitly to avoid relying on connection default
            $dbName = defined('DB_NAME') ? DB_NAME : null;
            $table = $dbName ? sprintf('`%s`.`%s`', str_replace('`', '', $dbName), 'vehicle_brands') : 'vehicle_brands';
            $stmt = $this->db->query('SHOW COLUMNS FROM ' . $table);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
                $columns[$column['Field']] = true;
            }
        } catch (\Throwable $e) {
            // If SHOW COLUMNS fails (older/alternate schema), assume common brand columns
            $columns = ['name' => true, 'name_fa' => true];
        }

        $this->vehicleBrandColumnsCache = $columns;

        return $columns;
    }

    private function getVehicleBrandNameExpression(string $tableAlias = 'vb'): string
    {
        $columns = $this->getVehicleBrandColumns();
        $parts = [];

        // Prefer localized/friendly columns when present
        foreach (['name_fa', 'name_en', 'name', 'slug'] as $column) {
            if (isset($columns[$column])) {
                $parts[] = $tableAlias ? $tableAlias . '.' . $column : $column;
            }
        }

        if ($parts === []) {
            return $tableAlias ? $tableAlias . '.name' : 'name';
        }

        return 'COALESCE(' . implode(', ', $parts) . ')';
    }

    public function getModelsByVehicleId($vehicleId)
    {
        $stmt = $this->db->prepare('SELECT * FROM vehicle_models WHERE id = ? LIMIT 1');
        $stmt->execute([$vehicleId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    protected function getBrandNameById(int $brandId): string
    {
        if ($brandId <= 0) {
            return '';
        }

        $stmt = $this->db->prepare(
            'SELECT ' . $this->getVehicleBrandNameExpression() . ' AS name FROM vehicle_brands WHERE id = ? LIMIT 1'
        );
        $stmt->execute([$brandId]);

        return (string) $stmt->fetchColumn();
    }

    protected function getVehicleBrandSelect(string $vehicleAlias = 'v'): string
    {
        // Build a safe brand select: prefer explicit brand column on vehicles, else use evaluated brand expression
        $brandExpr = $this->getVehicleBrandNameExpression('vb');
        return sprintf('%s.brand AS brand_name, %s AS brand_fallback', $vehicleAlias, $brandExpr);
    }

    private function getTableColumns(string $table): array
    {
        static $cache = [];

        if (!isset($cache[$table])) {
            $cache[$table] = [];
            try {
                $stmt = $this->db->query('SHOW COLUMNS FROM ' . $table);
                foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
                    $cache[$table][strtolower((string) $column['Field'])] = true;
                }
            } catch (\Throwable $e) {
                $cache[$table] = [];
            }
        }

        return $cache[$table];
    }

    private function hasTableColumn(string $table, string $column): bool
    {
        return isset($this->getTableColumns($table)[strtolower($column)]);
    }

    public function getVehicleStats($vehicleId)
    {
        $vehicleId = (int) $vehicleId;
        $stats = [
            'repair_count' => 0,
            'completed_repairs' => 0,
            'pending_repairs' => 0,
            'total_repair_cost' => 0.0,
            'maintenance_count' => 0,
            'latest_repair_date' => null,
            'latest_service_date' => null,
            'last_known_mileage' => null,
            'last_service' => null,
            'next_maintenance' => null,
            'parts_used' => 0,
            'status' => 'unknown',
            'plate_number' => null,
            'repair_status' => null,
        ];

        if ($vehicleId <= 0) {
            return $stats;
        }

        $repairStmt = $this->db->prepare(
            'SELECT COUNT(*) AS total_repairs, SUM(COALESCE(r.cost, 0)) AS total_cost, MAX(r.created_at) AS latest_repair_date, MAX(CASE WHEN r.status IN (\'completed\', \'delivered\') THEN 1 ELSE 0 END) AS completed_count, MIN(CASE WHEN r.status NOT IN (\'completed\', \'delivered\', \'cancelled\') THEN 1 ELSE 0 END) AS pending_flag FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id WHERE b.vehicle_id = ?'
        );
        $repairStmt->execute([$vehicleId]);
        $repairRow = $repairStmt->fetch(PDO::FETCH_ASSOC);
        if ($repairRow) {
            $stats['repair_count'] = (int) ($repairRow['total_repairs'] ?? 0);
            $stats['total_repair_cost'] = (float) ($repairRow['total_cost'] ?? 0);
            $stats['latest_repair_date'] = $repairRow['latest_repair_date'] ?? null;
            $stats['completed_repairs'] = (int) ($repairRow['completed_count'] ?? 0);
            $stats['pending_repairs'] = (int) ($repairRow['pending_flag'] ?? 0) === 1 ? 1 : 0;
            $stats['repair_status'] = $this->db->prepare('SELECT r.status FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id WHERE b.vehicle_id = ? ORDER BY r.created_at DESC LIMIT 1')->execute([$vehicleId]) ? null : null;
        }

        $pendingStmt = $this->db->prepare('SELECT COUNT(*) FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id WHERE b.vehicle_id = ? AND r.status NOT IN (\'completed\', \'delivered\', \'cancelled\')');
        $pendingStmt->execute([$vehicleId]);
        $stats['pending_repairs'] = (int) $pendingStmt->fetchColumn();

        $repairStatusStmt = $this->db->prepare('SELECT r.status FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id WHERE b.vehicle_id = ? ORDER BY r.created_at DESC LIMIT 1');
        $repairStatusStmt->execute([$vehicleId]);
        $stats['repair_status'] = $repairStatusStmt->fetchColumn() ?: null;

        $maintenanceStmt = $this->db->prepare('SELECT COUNT(*) AS total, MAX(service_date) AS latest_service_date, MAX(next_service_date) AS next_maintenance, MAX(mileage) AS last_known_mileage FROM maintenance_records WHERE vehicle_id = ?');
        $maintenanceStmt->execute([$vehicleId]);
        $maintenanceRow = $maintenanceStmt->fetch(PDO::FETCH_ASSOC);
        if ($maintenanceRow) {
            $stats['maintenance_count'] = (int) ($maintenanceRow['total'] ?? 0);
            $stats['latest_service_date'] = $maintenanceRow['latest_service_date'] ?? null;
            $stats['next_maintenance'] = $maintenanceRow['next_maintenance'] ?? null;
            $stats['last_known_mileage'] = $maintenanceRow['last_known_mileage'] ?? null;
            $stats['last_service'] = $maintenanceRow['latest_service_date'] ?? null;
        }

        $partsStmt = $this->db->prepare(
            'SELECT COUNT(*) FROM repair_parts rp LEFT JOIN repairs r ON r.id = rp.repair_id LEFT JOIN bookings b ON b.id = r.booking_id WHERE b.vehicle_id = ?'
        );
        $partsStmt->execute([$vehicleId]);
        $stats['parts_used'] = (int) $partsStmt->fetchColumn();

        $vehicleRow = $this->findById($vehicleId);
        if ($vehicleRow) {
            $stats['status'] = $vehicleRow['status'] ?? 'unknown';
            foreach (['plate_number', 'license_plate', 'plate', 'registration_number', 'vehicle_plate'] as $plateColumn) {
                if ($this->hasTableColumn('vehicles', $plateColumn)) {
                    $stats['plate_number'] = $vehicleRow[$plateColumn] ?? null;
                    break;
                }
            }
        }

        if (empty($stats['status']) || $stats['status'] === null) {
            $stats['status'] = 'unknown';
        }

        return $stats;
    }

    public function getUserVehicles($userId)
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT v.*, ' . $this->getVehicleBrandSelect('v') . ' FROM vehicles v LEFT JOIN vehicle_brands vb ON vb.id = v.brand_id WHERE v.user_id = ? ORDER BY v.created_at DESC'
            );
            $stmt->execute([(int) $userId]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            // Normalize: ensure brand_name present (fallback to brand_fallback or v.brand)
            foreach ($rows as &$r) {
                if (empty($r['brand_name'])) {
                    $r['brand_name'] = $r['brand_fallback'] ?? $r['brand'] ?? '';
                }
                unset($r['brand_fallback']);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAllVehicles()
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT v.*, u.name AS customer_name, ' . $this->getVehicleBrandSelect('v') . ' FROM vehicles v LEFT JOIN vehicle_brands vb ON vb.id = v.brand_id LEFT JOIN users u ON u.id = v.user_id ORDER BY v.created_at DESC'
            );
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as &$r) {
                if (empty($r['brand_name'])) {
                    $r['brand_name'] = $r['brand_fallback'] ?? $r['brand'] ?? '';
                }
                unset($r['brand_fallback']);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT v.*, u.name AS customer_name, ' . $this->getVehicleBrandSelect('v') . ' FROM vehicles v LEFT JOIN vehicle_brands vb ON vb.id = v.brand_id LEFT JOIN users u ON u.id = v.user_id WHERE v.id = ? LIMIT 1'
            );
            $stmt->execute([(int) $id]);
            $r = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($r) {
                if (empty($r['brand_name'])) {
                    $r['brand_name'] = $r['brand_fallback'] ?? $r['brand'] ?? '';
                }
                unset($r['brand_fallback']);
            }
            return $r;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function createVehicle(array $data)
    {
        $brandId = (int) ($data['brand_id'] ?? 0);
        $brandName = $this->getBrandNameById($brandId);

        $stmt = $this->db->prepare(
            'INSERT INTO vehicles (user_id, brand_id, brand, model, year, engine, vin, mileage, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            (int) ($data['user_id'] ?? 0),
            $brandId,
            $brandName ?: null,
            trim($data['model'] ?? ''),
            trim($data['year'] ?? ''),
            trim($data['engine'] ?? ''),
            trim($data['vin'] ?? ''),
            (int) ($data['mileage'] ?? 0),
            date('Y-m-d H:i:s'),
        ]);
    }

    public function updateMileage($vehicleId, $mileage)
    {
        $stmt = $this->db->prepare('UPDATE vehicles SET mileage = ? WHERE id = ?');
        return $stmt->execute([(int) $mileage, (int) $vehicleId]);
    }

    public function getMaintenanceStatus($vehicleId)
    {
        $stmt = $this->db->prepare('SELECT mileage FROM vehicles WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $vehicleId]);
        $mileage = (int) $stmt->fetchColumn();

        if ($mileage <= 0) {
            return 'unknown';
        }

        if ($mileage > 15000) {
            return 'needs_service';
        }

        return 'healthy';
    }

    public function getServiceHistory($vehicleId)
    {
        $stmt = $this->db->prepare(
            'SELECT mr.*, v.model FROM maintenance_records mr LEFT JOIN vehicles v ON v.id = mr.vehicle_id WHERE mr.vehicle_id = ? ORDER BY mr.service_date DESC'
        );
        $stmt->execute([(int) $vehicleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehicleProfile($vehicleId)
    {
        $vehicle = $this->findById((int) $vehicleId);
        if (!$vehicle) {
            return false;
        }

        $stmt = $this->db->prepare(
            'SELECT * FROM vehicle_profiles WHERE vehicle_id = ? LIMIT 1'
        );
        $stmt->execute([(int) $vehicleId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC);

        $stats = $this->getVehicleStats((int) $vehicleId);
        foreach (['status', 'plate_number', 'repair_count', 'completed_repairs', 'pending_repairs', 'total_repair_cost', 'maintenance_count', 'latest_repair_date', 'latest_service_date', 'last_known_mileage', 'last_service', 'next_maintenance', 'parts_used', 'repair_status'] as $key) {
            if (array_key_exists($key, $stats)) {
                $vehicle[$key] = $stats[$key];
            }
        }

        $vehicle['profile'] = $profile ?: [];
        $vehicle['maintenance_status'] = $this->getMaintenanceStatus((int) $vehicleId);
        $vehicle['service_history'] = $this->getServiceHistory((int) $vehicleId);
        $vehicle['diagnostic_history'] = $this->getDiagnosticHistory((int) $vehicleId);
        $vehicle['last_scan_date'] = $this->getLastScanDate((int) $vehicleId);
        $vehicle['health_score'] = $this->getHealthScore((int) $vehicleId);
        $vehicle['recommended_products'] = $this->getVehicleAwareSuggestions((int) $vehicleId, 4);
        $vehicle['profile_stats'] = $stats;

        return $vehicle;
    }

    public function getVehicleAwareSuggestions($vehicleId, $limit = 4)
    {
        $vehicle = $this->findById((int) $vehicleId);
        if (!$vehicle) {
            return [];
        }

        $modelId = (int) ($vehicle['model_id'] ?? 0);
        $brandId = (int) ($vehicle['brand_id'] ?? 0);
        $year = isset($vehicle['year']) && $vehicle['year'] !== '' ? (int) $vehicle['year'] : null;
        $engineType = trim((string) ($vehicle['engine_type'] ?? $vehicle['engine'] ?? ''));
        $limit = max(1, (int) $limit);

        $sql = 'SELECT p.* FROM products p WHERE p.status = 1';
        $params = [];

        if ($modelId > 0) {
            $sql .= ' AND (
                EXISTS (SELECT 1 FROM product_compatibility pc WHERE pc.product_id = p.id AND pc.model_id = ?)
                OR EXISTS (SELECT 1 FROM product_compatibility pc INNER JOIN vehicle_models vm ON vm.id = pc.model_id WHERE pc.product_id = p.id AND vm.brand_id = ?)
            )';
            $params[] = $modelId;
            $params[] = $brandId > 0 ? $brandId : 0;
        } elseif ($brandId > 0) {
            $sql .= ' AND EXISTS (SELECT 1 FROM product_compatibility pc INNER JOIN vehicle_models vm ON vm.id = pc.model_id WHERE pc.product_id = p.id AND vm.brand_id = ?)';
            $params[] = $brandId;
        } else {
            return [];
        }

        if ($year !== null) {
            $sql .= ' AND EXISTS (SELECT 1 FROM product_compatibility pc WHERE pc.product_id = p.id AND (pc.year IS NULL OR pc.year = ?))';
            $params[] = $year;
        }

        if ($engineType !== '') {
            $sql .= ' AND EXISTS (SELECT 1 FROM product_compatibility pc WHERE pc.product_id = p.id AND (pc.engine_type IS NULL OR LOWER(pc.engine_type) = LOWER(?)))';
            $params[] = $engineType;
        }

        $sql .= ' ORDER BY p.created_at DESC LIMIT ?';
        $params[] = $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getDiagnosticHistory($vehicleId)
    {
        $stmt = $this->db->prepare(
            'SELECT dr.*, oc.code, oc.title_fa, oc.severity FROM diagnostic_results dr LEFT JOIN diagnostic_sessions ds ON ds.id = dr.session_id LEFT JOIN obd_error_codes oc ON oc.id = dr.error_code_id WHERE ds.vehicle_id = ? ORDER BY dr.created_at DESC'
        );
        $stmt->execute([(int) $vehicleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLastScanDate($vehicleId)
    {
        $stmt = $this->db->prepare('SELECT MAX(created_at) AS last_scan FROM diagnostic_sessions WHERE vehicle_id = ?');
        $stmt->execute([(int) $vehicleId]);
        return $stmt->fetchColumn() ?: null;
    }

    public function getHealthScore($vehicleId)
    {
        $history = $this->getDiagnosticHistory((int) $vehicleId);
        if (empty($history)) {
            return 100;
        }

        $score = 100;
        foreach ($history as $item) {
            $severity = strtolower((string) ($item['severity'] ?? 'unknown'));
            if ($severity === 'critical') {
                $score -= 25;
            } elseif ($severity === 'high') {
                $score -= 15;
            } elseif ($severity === 'medium') {
                $score -= 8;
            }
        }

        return max(0, min(100, $score));
    }

    public function updateVehicle($id, array $data)
    {
        $brandId = (int) ($data['brand_id'] ?? 0);
        $brandName = $this->getBrandNameById($brandId);

        $stmt = $this->db->prepare(
            'UPDATE vehicles SET brand_id = ?, brand = ?, model = ?, year = ?, engine = ?, vin = ?, mileage = ? WHERE id = ?'
        );

        return $stmt->execute([
            $brandId,
            $brandName ?: null,
            trim($data['model'] ?? ''),
            trim($data['year'] ?? ''),
            trim($data['engine'] ?? ''),
            trim($data['vin'] ?? ''),
            (int) ($data['mileage'] ?? 0),
            (int) $id,
        ]);
    }

    public function deleteVehicle($id)
    {
        $stmt = $this->db->prepare('DELETE FROM vehicles WHERE id = ?');
        return $stmt->execute([(int) $id]);
    }

    public function getVehicleHistory($vehicleId)
    {
        // Build a schema-aware query: some installations may omit booking_date or repairs.created_at
        $bookingCols = $this->getTableColumns('bookings');
        $repairCols = $this->getTableColumns('repairs');

        $select = ['b.id', 'b.status', 'b.problem'];
        if (isset($bookingCols['booking_date'])) {
            $select[] = 'b.booking_date';
        }

        $select[] = 'r.id AS repair_id';
        if (isset($repairCols['diagnosis'])) {
            $select[] = 'r.diagnosis';
        }
        if (isset($repairCols['repair_notes'])) {
            $select[] = 'r.repair_notes';
        }
        if (isset($repairCols['cost'])) {
            $select[] = 'r.cost';
        }
        if (isset($repairCols['status'])) {
            $select[] = 'r.status AS repair_status';
        }

        $select[] = 's.title AS service_title';

        $order = [];
        if (isset($bookingCols['booking_date'])) {
            $order[] = 'b.booking_date DESC';
        }
        if (isset($repairCols['created_at'])) {
            $order[] = 'r.created_at DESC';
        }
        if (empty($order)) {
            $order[] = 'b.id DESC';
        }

        $sql = sprintf('SELECT %s FROM bookings b LEFT JOIN repairs r ON r.booking_id = b.id LEFT JOIN services s ON s.id = b.service_id WHERE b.vehicle_id = ? ORDER BY %s', implode(', ', $select), implode(', ', $order));

        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([(int) $vehicleId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // If schema mismatch causes query failure, return an empty history rather than fatally crashing
            return [];
        }
    }

    public function getBrands()
    {
        try {
            $nameExpr = $this->getVehicleBrandNameExpression('');
            $sql = sprintf('SELECT id, %s AS name FROM vehicle_brands WHERE status = 1 ORDER BY name ASC', $nameExpr);
            $stmt = $this->db->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            // Fallback: try conservative column names
            try {
                $stmt = $this->db->prepare('SELECT id, name_fa AS name FROM vehicle_brands WHERE status = 1 ORDER BY name ASC');
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e) {
                return [];
            }
        }
    }

    public function getUpcomingMaintenance($userId)
    {
        $stmt = $this->db->prepare(
            'SELECT b.id, b.booking_date, b.status, s.title AS service_title FROM bookings b LEFT JOIN services s ON s.id = b.service_id WHERE b.user_id = ? AND b.status IN (\'pending\', \'confirmed\', \'in_progress\') ORDER BY b.booking_date ASC LIMIT 5'
        );
        $stmt->execute([(int) $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function validateVehicleData(array $data)
    {
        $errors = [];

        if (trim($data['brand_id'] ?? '') === '') {
            $errors[] = 'انتخاب برند خودرو الزامی است.';
        }

        if (trim($data['model'] ?? '') === '') {
            $errors[] = 'مدل خودرو الزامی است.';
        }

        if (trim($data['year'] ?? '') === '') {
            $errors[] = 'سال ساخت خودرو الزامی است.';
        }

        if (trim($data['mileage'] ?? '') === '') {
            $errors[] = 'کیلومتر خودرو الزامی است.';
        } elseif (!is_numeric($data['mileage'] ?? null)) {
            $errors[] = 'کیلومتر باید عددی باشد.';
        }

        return $errors;
    }
}

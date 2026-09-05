<?php

namespace App\Models;

use PDO;

class ProductCompatibility extends Model
{
    public function getByProductId($productId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM product_compatibility WHERE product_id = ? ORDER BY id ASC');
            $stmt->execute([(int) $productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getByModelId($modelId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM product_compatibility WHERE model_id = ? ORDER BY id ASC');
            $stmt->execute([(int) $modelId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getCompatibleVehicleModels($productId)
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT pc.*, vm.id AS model_id, vm.name_fa, COALESCE(vm.name_en, vm.name_fa, vm.slug) AS name_en, vm.slug, vm.year_from, vm.year_to, vm.engine_type,
                 COALESCE(vb.name_fa, vb.name, vb.name_en, vb.slug) AS brand_name_fa, COALESCE(vb.name_en, vb.name, vb.name_fa, vb.slug) AS brand_name_en
                 FROM product_compatibility pc
                 LEFT JOIN vehicle_models vm ON vm.id = pc.model_id
                 LEFT JOIN vehicle_brands vb ON vb.id = vm.brand_id
                 WHERE pc.product_id = ?
                 ORDER BY brand_name_fa ASC, vm.name_fa ASC, pc.year ASC'
            );
            $stmt->execute([(int) $productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Fallback: try a conservative select avoiding optional columns
            try {
                $stmt = $this->db->prepare(
                    'SELECT pc.*, vm.id AS model_id, vm.name_fa, vm.slug
                     FROM product_compatibility pc
                     LEFT JOIN vehicle_models vm ON vm.id = pc.model_id
                     WHERE pc.product_id = ?
                     ORDER BY vm.name_fa ASC, pc.year ASC'
                );
                $stmt->execute([(int) $productId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    public function addRelation($productId, $modelId, $year = null, $engineType = null)
    {
        $yearValue = $year !== null && $year !== '' ? trim((string) $year) : null;
        $engineTypeValue = $engineType !== null && $engineType !== '' ? trim((string) $engineType) : null;

        try {
            $stmt = $this->db->prepare(
                'INSERT INTO product_compatibility (product_id, model_id, year, engine_type, created_at)
                 VALUES (?, ?, ?, ?, ?)
                 ON DUPLICATE KEY UPDATE year = VALUES(year), engine_type = VALUES(engine_type)'
            );
            return $stmt->execute([
                (int) $productId,
                (int) $modelId,
                $yearValue,
                $engineTypeValue,
                date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            try {
                $stmt = $this->db->prepare(
                    'INSERT INTO product_compatibility (product_id, model_id, year, engine_type)
                     VALUES (?, ?, ?, ?)
                     ON DUPLICATE KEY UPDATE year = VALUES(year), engine_type = VALUES(engine_type)'
                );
                return $stmt->execute([
                    (int) $productId,
                    (int) $modelId,
                    $yearValue,
                    $engineTypeValue,
                ]);
            } catch (\Throwable $e2) {
                return false;
            }
        }
    }

    public function removeRelation($productId, $modelId)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM product_compatibility WHERE product_id = ? AND model_id = ?');
            return $stmt->execute([(int) $productId, (int) $modelId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function replaceRelations($productId, array $modelIds)
    {
        $productId = (int) $productId;
        if ($productId <= 0) {
            return false;
        }

        $normalizedIds = [];
        foreach ((array) $modelIds as $modelId) {
            $modelId = (int) $modelId;
            if ($modelId > 0) {
                $normalizedIds[$modelId] = $modelId;
            }
        }

        try {
            $this->db->beginTransaction();

            $deleteStmt = $this->db->prepare('DELETE FROM product_compatibility WHERE product_id = ?');
            $deleteStmt->execute([$productId]);

            if (!empty($normalizedIds)) {
                $sqlWithCreatedAt = 'INSERT INTO product_compatibility (product_id, model_id, year, engine_type, created_at) VALUES (?, ?, NULL, NULL, ?)';
                $sqlWithoutCreatedAt = 'INSERT INTO product_compatibility (product_id, model_id, year, engine_type) VALUES (?, ?, NULL, NULL)';
                $insertStmt = $this->db->prepare($sqlWithCreatedAt);
                $createdAt = date('Y-m-d H:i:s');

                foreach ($normalizedIds as $modelId) {
                    try {
                        $insertStmt->execute([$productId, $modelId, $createdAt]);
                    } catch (\Throwable $e) {
                        $fallbackStmt = $this->db->prepare($sqlWithoutCreatedAt);
                        $fallbackStmt->execute([$productId, $modelId]);
                    }
                }
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            if ($this->db && $this->db->inTransaction()) {
                $this->db->rollBack();
            }
            return false;
        }
    }

    public function getVehicleModelOptions()
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT vm.id, vm.name_fa, COALESCE(vm.name_en, vm.name_fa, vm.slug) AS name_en, vm.slug, vm.year_from, vm.year_to, vm.engine_type,
                 COALESCE(vb.name_fa, vb.name, vb.name_en, vb.slug) AS brand_name_fa, COALESCE(vb.name_en, vb.name, vb.name_fa, vb.slug) AS brand_name_en
                 FROM vehicle_models vm
                 LEFT JOIN vehicle_brands vb ON vb.id = vm.brand_id
                 WHERE vm.status = 1
                 ORDER BY brand_name_fa ASC, vm.name_fa ASC, vm.year_from ASC, vm.id ASC'
            );
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Fallback to minimal columns
            try {
                $stmt = $this->db->prepare('SELECT vm.id, vm.name_fa, vm.slug, vm.year_from FROM vehicle_models vm WHERE vm.status = 1 ORDER BY vm.name_fa ASC, vm.year_from ASC, vm.id ASC');
                $stmt->execute();
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    public function getProductsByVehicle($vehicleModelId, $page = 1, $perPage = 12, array $filters = [])
    {
        $modelId = (int) $vehicleModelId;
        if ($modelId <= 0) {
            return ['products' => [], 'total' => 0];
        }

        try {
            $page = max(1, (int) $page);
            $perPage = max(1, (int) $perPage);
            $offset = ($page - 1) * $perPage;
            $year = trim((string) ($filters['vehicle_year'] ?? ''));

            $sql = 'SELECT p.* FROM product_compatibility pc INNER JOIN products p ON p.id = pc.product_id WHERE p.status = 1 AND pc.model_id = ?';
            $params = [$modelId];

            if ($year !== '') {
                $sql .= ' AND (pc.year IS NULL OR pc.year = ?)';
                $params[] = $year;
            }

            $countSql = 'SELECT COUNT(*) FROM product_compatibility pc INNER JOIN products p ON p.id = pc.product_id WHERE p.status = 1 AND pc.model_id = ?';
            $countParams = [$modelId];

            if ($year !== '') {
                $countSql .= ' AND (pc.year IS NULL OR pc.year = ?)';
                $countParams[] = $year;
            }

            $countStmt = $this->db->prepare($countSql);
            $countStmt->execute($countParams);
            $total = (int) $countStmt->fetchColumn();

            $sql .= ' ORDER BY p.created_at DESC LIMIT ' . $offset . ', ' . $perPage;
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return ['products' => $rows, 'total' => $total];
        } catch (\Throwable $e) {
            return ['products' => [], 'total' => 0];
        }
    }
}
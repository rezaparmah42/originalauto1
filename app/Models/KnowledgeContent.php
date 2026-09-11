<?php
namespace App\Models;

class KnowledgeContent extends Model
{
    private function normalizeSlug(string $value): string
    {
        $value = trim((string) $value);
        if ($value === '') {
            return '';
        }

        $value = str_replace(['_', ' '], '-', $value);
        $value = preg_replace('/[^a-zA-Z0-9\-]+/', '-', $value);
        $value = preg_replace('/-+/', '-', $value);
        $value = trim($value, '-');

        return strtolower($value);
    }

    private function tableExists(string $table): bool
    {
        try {
            $stmt = $this->db->query('SHOW TABLES LIKE ' . $this->db->quote($table));
            return $stmt !== false && $stmt->fetchColumn() !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function validateCanonicalModel(int $modelId): bool
    {
        if ($modelId <= 0 || !$this->tableExists('vehicle_models')) {
            return false;
        }

        try {
            $stmt = $this->db->prepare('SELECT 1 FROM vehicle_models WHERE id = ? AND status = 1 LIMIT 1');
            $stmt->execute([$modelId]);
            return (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function validateCanonicalService(int $serviceId): bool
    {
        if ($serviceId <= 0 || !$this->tableExists('services')) {
            return false;
        }

        try {
            $stmt = $this->db->prepare('SELECT 1 FROM services WHERE id = ? AND status = 1 LIMIT 1');
            $stmt->execute([$serviceId]);
            return (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function validateVehicleServiceRelationship(int $modelId, int $serviceId): bool
    {
        if ($modelId <= 0 || $serviceId <= 0 || !$this->tableExists('vehicle_model_service')) {
            return false;
        }

        try {
            $stmt = $this->db->prepare('SELECT 1 FROM vehicle_model_service WHERE model_id = ? AND service_id = ? LIMIT 1');
            $stmt->execute([$modelId, $serviceId]);
            return (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function hasCanonicalMappingForType(array $record): bool
    {
        if (empty($record['vehicle_model_id'])) {
            return false;
        }

        $modelId = (int) $record['vehicle_model_id'];
        if (!$this->validateCanonicalModel($modelId)) {
            return false;
        }

        $serviceId = isset($record['service_id']) ? (int) $record['service_id'] : 0;
        if ($serviceId > 0) {
            if (!$this->validateCanonicalService($serviceId)) {
                return false;
            }
            if (!$this->validateVehicleServiceRelationship($modelId, $serviceId)) {
                return false;
            }
        }

        $type = strtolower((string) ($record['content_type'] ?? ''));
        $validTypes = ['overview', 'technical-specifications', 'engine', 'transmission', 'electrical', 'ecu', 'diagnostics', 'dtc', 'common-problems', 'symptoms', 'repair-solutions', 'maintenance', 'services', 'faq', 'articles'];
        if ($type === '' || in_array($type, $validTypes, true)) {
            return true;
        }

        $typeMap = [
            'common-problem' => 'vehicle_common_problems',
            'common-problems' => 'vehicle_common_problems',
            'symptom' => 'vehicle_symptoms',
            'symptoms' => 'vehicle_symptoms',
            'dtc-code' => 'vehicle_dtc_codes',
            'dtc' => 'vehicle_dtc_codes',
            'diagnostic' => 'vehicle_diagnostics',
            'diagnostics' => 'vehicle_diagnostics',
            'ecu-info' => 'vehicle_ecu_info',
            'ecu' => 'vehicle_ecu_info',
            'maintenance-task' => 'vehicle_maintenance_tasks',
            'maintenance' => 'vehicle_maintenance_tasks',
            'repair-solution' => 'vehicle_repair_solutions',
            'repair-solutions' => 'vehicle_repair_solutions',
            'faq' => 'vehicle_model_faqs',
            'article' => 'articles',
        ];

        $table = $typeMap[$type] ?? null;
        if ($table === null) {
            return false;
        }

        if ($table === 'articles' || !$this->tableExists($table)) {
            return $table === 'articles' ? true : false;
        }

        try {
            $stmt = $this->db->prepare('SELECT 1 FROM ' . $table . ' WHERE model_id = ? LIMIT 1');
            $stmt->execute([$modelId]);
            return (bool) $stmt->fetchColumn();
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function isPubliclyPublishable(array $record): bool
    {
        if (!is_array($record)) {
            return false;
        }

        $status = strtolower((string) ($record['status'] ?? ''));
        if ($status !== 'published') {
            return false;
        }

        if (!$this->hasCanonicalMappingForType($record)) {
            return false;
        }

        if (!empty($record['source_document_id'])) {
            $sourceId = (int) $record['source_document_id'];
            if ($sourceId > 0 && $this->tableExists('knowledge_source_documents')) {
                try {
                    $stmt = $this->db->prepare('SELECT 1 FROM knowledge_source_documents WHERE id = ? LIMIT 1');
                    $stmt->execute([$sourceId]);
                    if (!$stmt->fetchColumn()) {
                        return false;
                    }
                } catch (\Throwable $e) {
                    return false;
                }
            }
        }

        return true;
    }

    public function findPublished(string $slug, ?string $brand = null, ?string $model = null): ?array
    {
        if (!$this->tableExists('vehicle_knowledge_contents')) {
            return null;
        }

        $sql = 'SELECT k.*, vm.slug AS model_slug, vm.name_fa AS model_name, vb.slug AS brand_slug, vb.name_fa AS brand_name
                FROM vehicle_knowledge_contents k
                JOIN vehicle_models vm ON vm.id = k.vehicle_model_id
                JOIN vehicle_brands vb ON vb.id = vm.brand_id
                WHERE k.slug = ? AND k.status = "published"';
        $params = [$slug];

        if ($brand !== null && $brand !== '') {
            $sql .= ' AND vb.slug = ?';
            $params[] = $this->normalizeSlug($brand);
        }

        if ($model !== null && $model !== '') {
            $sql .= ' AND vm.slug = ?';
            $params[] = $this->normalizeSlug($model);
        }

        $sql .= ' LIMIT 1';
        $s = $this->db->prepare($sql);
        $s->execute($params);

        $record = $s->fetch();
        if (!$record) {
            return null;
        }

        return $this->isPubliclyPublishable($record) ? $record : null;
    }

    public function byModel(string $model): array
    {
        if (!$this->tableExists('vehicle_knowledge_contents')) {
            return [];
        }

        $modelSlug = $this->normalizeSlug($model);
        if ($modelSlug === '') {
            return [];
        }

        $s = $this->db->prepare('SELECT k.*, vm.slug AS model_slug, vm.name_fa AS model_name, vb.slug AS brand_slug, vb.name_fa AS brand_name
            FROM vehicle_knowledge_contents k
            JOIN vehicle_models vm ON vm.id = k.vehicle_model_id
            JOIN vehicle_brands vb ON vb.id = vm.brand_id
            WHERE (vm.slug = ? OR LOWER(vm.slug) = LOWER(?)) AND k.status = "published"
            ORDER BY k.content_type, k.title_fa');
        $s->execute([$modelSlug, $modelSlug]);

        $items = $s->fetchAll();
        return array_values(array_filter($items, function ($record) {
            return $this->isPubliclyPublishable($record);
        }));
    }

    public function byVehicle(string $brand, string $model): array
    {
        if (!$this->tableExists('vehicle_knowledge_contents')) {
            return [];
        }

        $brandSlug = $this->normalizeSlug($brand);
        $modelSlug = $this->normalizeSlug($model);
        if ($brandSlug === '' || $modelSlug === '') {
            return [];
        }

        $s = $this->db->prepare('SELECT k.*, vm.slug AS model_slug, vm.name_fa AS model_name, vb.slug AS brand_slug, vb.name_fa AS brand_name
            FROM vehicle_knowledge_contents k
            JOIN vehicle_models vm ON vm.id = k.vehicle_model_id
            JOIN vehicle_brands vb ON vb.id = vm.brand_id
            WHERE (vb.slug = ? OR LOWER(vb.slug) = LOWER(?))
              AND (vm.slug = ? OR LOWER(vm.slug) = LOWER(?))
              AND k.status = "published"
            ORDER BY k.content_type, k.title_fa');
        $s->execute([$brandSlug, $brandSlug, $modelSlug, $modelSlug]);

        $items = $s->fetchAll();
        return array_values(array_filter($items, function ($record) {
            return $this->isPubliclyPublishable($record);
        }));
    }
}

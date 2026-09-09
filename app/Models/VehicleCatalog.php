<?php

namespace App\Models;

use PDO;

class VehicleCatalog extends Model
{
    private $variantModel;

    private function hasTable(string $table): bool
    {
        try {
            // Qualify the database to avoid relying on connection default database
            $dbName = defined('DB_NAME') ? DB_NAME : null;
            if ($dbName) {
                $sql = 'SHOW TABLES FROM `' . str_replace('`', '', $dbName) . '` LIKE ' . $this->db->quote($table);
            } else {
                $sql = 'SHOW TABLES LIKE ' . $this->db->quote($table);
            }
            $stmt = $this->db->query($sql);
            return $stmt->fetchColumn() !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function hasColumn(string $table, string $column): bool
    {
        try {
            $dbName = defined('DB_NAME') ? DB_NAME : null;
            $tbl = $dbName ? sprintf('`%s`.`%s`', str_replace('`', '', $dbName), str_replace('`', '', $table)) : ('`' . str_replace('`', '', $table) . '`');
            $stmt = $this->db->query('SHOW COLUMNS FROM ' . $tbl . ' LIKE ' . $this->db->quote($column));
            return $stmt->fetchColumn() !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    private function getTableColumns(string $table): array
    {
        static $cache = [];

        if (isset($cache[$table])) {
            return $cache[$table];
        }

        $cache[$table] = [];
        try {
            $dbName = defined('DB_NAME') ? DB_NAME : null;
            $tbl = $dbName ? sprintf('`%s`.`%s`', str_replace('`', '', $dbName), str_replace('`', '', $table)) : ('`' . str_replace('`', '', $table) . '`');
            $stmt = $this->db->query('SHOW COLUMNS FROM ' . $tbl);
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
                $cache[$table][$column['Field']] = true;
            }
        } catch (\Throwable $e) {
            $cache[$table] = [];
        }

        return $cache[$table];
    }

    private function chooseColumn(array $columns, array $candidates, string $fallback = ''): string
    {
        foreach ($candidates as $candidate) {
            if (isset($columns[$candidate])) {
                return $candidate;
            }
        }

        return $fallback;
    }

    private function getBrandNameExpression(string $alias = 'vb'): string
    {
        // Build a COALESCE expression over available brand name columns
        $columns = $this->getTableColumns('vehicle_brands');
        $cands = [];
        foreach (['name_fa', 'name', 'name_en', 'slug'] as $c) {
            if (isset($columns[$c])) {
                $cands[] = ($alias ? ($alias . '.' . $c) : $c);
            }
        }
        if (empty($cands)) {
            // fallback to id for deterministic output
            return ($alias ? ($alias . '.id') : 'id');
        }
        return 'COALESCE(' . implode(', ', $cands) . ')';
    }

    private function getModelNameExpression(string $alias = 'vm'): string
    {
        // Build a COALESCE expression over available model name columns
        $columns = $this->getTableColumns('vehicle_models');
        $cands = [];
        foreach (['name_fa', 'common_name_fa', 'name', 'name_en', 'common_name_en', 'slug'] as $c) {
            if (isset($columns[$c])) {
                $cands[] = ($alias ? ($alias . '.' . $c) : $c);
            }
        }
        if (empty($cands)) {
            return ($alias ? ($alias . '.id') : 'id');
        }
        return 'COALESCE(' . implode(', ', $cands) . ')';
    }

    public function getBrandCatalog(): array
    {
        if (!$this->hasTable('vehicle_models') || !$this->hasTable('vehicle_brands')) {
            return [];
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $sql = 'SELECT vb.id, vb.slug, vb.category, COALESCE(vb.name_fa, vb.name, vb.name_en, vb.slug) AS name_fa, COALESCE(vb.name_en, vb.name, vb.name_fa, vb.slug) AS name_en, COUNT(vm.id) AS model_count FROM vehicle_brands vb LEFT JOIN vehicle_models vm ON vm.brand_id = vb.id AND vm.status = 1 WHERE vb.status = 1 GROUP BY vb.id ORDER BY name_fa ASC, name_en ASC';
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getBrandBySlug(string $slug): ?array
    {
        $slug = trim((string) $slug);
        if ($slug === '') {
            return null;
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM vehicle_brands WHERE status = 1 AND (LOWER(slug) = LOWER(?) OR LOWER(name_fa) = LOWER(?) OR LOWER(name_en) = LOWER(?)) LIMIT 1');
            $stmt->execute([$slug, $slug, $slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getModelBySlug(string $slug): ?array
    {
        $slug = trim((string) $slug);
        if ($slug === '') {
            return null;
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $modelExpr = $this->getModelNameExpression('vm');
            $stmt = $this->db->prepare('SELECT vm.*, ' . $brandExpr . ' AS brand, COALESCE(vb.name_fa, vb.name, vb.name_en, vb.slug) AS brand_name_fa, COALESCE(vb.name_en, vb.name, vb.name_fa, vb.slug) AS brand_name_en FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND (LOWER(vm.slug) = LOWER(?) OR LOWER(' . $modelExpr . ') = LOWER(?)) LIMIT 1');
            $stmt->execute([$slug, $slug]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ?: null;
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function getActiveMatrixModels(): array
    {
        $roots = [
            __DIR__ . '/../Data/generated_models',
            __DIR__ . '/../Data/generated_matrices',
        ];

        $seen = [];
        $models = [];

        foreach ($roots as $root) {
            if (!is_dir($root)) {
                continue;
            }

            $brandDirs = glob($root . '/*', GLOB_ONLYDIR);
            if (!$brandDirs) {
                continue;
            }

            foreach ($brandDirs as $brandDir) {
                foreach (glob($brandDir . '/*.php') as $file) {
                    $slug = strtolower(basename($file, '.php'));
                    if ($slug === '' || isset($seen[$slug])) {
                        continue;
                    }

                    $match = $this->getModelBySlug($slug);
                    if ($match) {
                        $models[] = $match;
                        $seen[$slug] = true;
                    }
                }
            }
        }

        return $models;
    }

    public function getAll()
    {
        if (!$this->hasTable('vehicle_models') || !$this->hasTable('vehicle_brands')) {
            return [];
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $modelExpr = $this->getModelNameExpression('vm');
            $sql = sprintf(
                'SELECT vm.id, %s AS brand, %s AS model, vm.year_from AS year_start, vm.year_to AS year_end, vm.engine_type, vm.status FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 ORDER BY vm.id DESC',
                $brandExpr,
                $modelExpr
            );
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getModelsByBrand($brand)
    {
        $brand = trim((string) $brand);
        if ($brand === '') {
            return $this->getAll();
        }
        $brandExpr = $this->getBrandNameExpression('vb');
        $modelExpr = $this->getModelNameExpression('vm');

        // Determine concrete brand/model columns to avoid referencing missing columns
        $brandCols = $this->getTableColumns('vehicle_brands');
        $modelCols = $this->getTableColumns('vehicle_models');
        $brandCol = $this->chooseColumn($brandCols, ['name_fa', 'name_en', 'name', 'slug'], 'slug');
        $modelCol = $this->chooseColumn($modelCols, ['name_fa', 'name_en', 'common_name_fa', 'common_name_en', 'name', 'slug'], 'slug');

        $sql = 'SELECT vm.id, ' . $brandExpr . ' AS brand, vb.slug AS brand_slug, ' . $modelExpr . ' AS model, vm.`' . $modelCol . '` AS slug, vm.slug AS model_slug, vm.year_from AS year_start, vm.year_to AS year_end, vm.engine_type, vm.body_type, vm.status FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND (LOWER(' . $brandExpr . ') = LOWER(?) OR LOWER(vb.`' . $brandCol . '`) = LOWER(?) OR LOWER(vb.slug) = LOWER(?)) ORDER BY model';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$brand, $brand, $brand]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function search($term = '', array $filters = [], $page = 1, $perPage = 18)
    {
        $term = trim((string) $term);
        $page = max(1, (int) $page);
        $perPage = max(1, min(50, (int) $perPage));
        $offset = ($page - 1) * $perPage;
        $brandExpr = $this->getBrandNameExpression('vb');
        $modelExpr = $this->getModelNameExpression('vm');
        $where = ['vm.status = 1', 'vb.status = 1'];
        $params = [];

        if ($term !== '') {
            $brandCols = $this->getTableColumns('vehicle_brands');
            $modelCols = $this->getTableColumns('vehicle_models');
            $brandCol = $this->chooseColumn($brandCols, ['name_fa', 'name_en', 'name', 'slug'], 'slug');
            $modelCol = $this->chooseColumn($modelCols, ['name_fa', 'name_en', 'common_name_fa', 'common_name_en', 'name', 'slug'], 'slug');

            $where[] = '(' . implode(' OR ', [
                $brandExpr . ' LIKE ?',
                'vb.`' . $brandCol . '` LIKE ?',
                $modelExpr . ' LIKE ?',
                'vm.`' . $modelCol . '` LIKE ?',
                'vm.`' . $modelCol . '` LIKE ?',
                'vm.engine_type LIKE ?',
            ]) . ')';
            for ($i = 0; $i < 6; $i++) {
                $params[] = '%' . $term . '%';
            }
        }

        if (!empty($filters['brand'])) {
            $brandCols = $this->getTableColumns('vehicle_brands');
            $brandCol = $this->chooseColumn($brandCols, ['name_fa', 'name_en', 'name', 'slug'], 'slug');
            $where[] = '(LOWER(' . $brandExpr . ') = LOWER(?) OR LOWER(vb.`' . $brandCol . '`) = LOWER(?))';
            $params[] = trim((string) $filters['brand']);
            $params[] = trim((string) $filters['brand']);
        }
        if (!empty($filters['model'])) {
            $modelCols = $this->getTableColumns('vehicle_models');
            $modelCol = $this->chooseColumn($modelCols, ['name_fa', 'name_en', 'common_name_fa', 'common_name_en', 'name', 'slug'], 'slug');
            $where[] = '(LOWER(' . $modelExpr . ') = LOWER(?) OR LOWER(vm.`' . $modelCol . '`) = LOWER(?))';
            $params[] = trim((string) $filters['model']);
            $params[] = trim((string) $filters['model']);
        }
        if (!empty($filters['engine'])) {
            $where[] = 'vm.engine_type = ?';
            $params[] = trim((string) $filters['engine']);
        }
        if (!empty($filters['year']) && ctype_digit((string) $filters['year'])) {
            $where[] = '(vm.year_from IS NULL OR vm.year_from <= ?) AND (vm.year_to IS NULL OR vm.year_to >= ?)';
            $params[] = (int) $filters['year'];
            $params[] = (int) $filters['year'];
        }

        $from = ' FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE ' . implode(' AND ', $where);
        $countStmt = $this->db->prepare('SELECT COUNT(*)' . $from);
        $countStmt->execute($params);
        $total = (int) $countStmt->fetchColumn();

        // Build a safe select list based on available model columns
        $modelCols = $this->getTableColumns('vehicle_models');
        $selectCols = ['vm.id', 'vm.brand_id', $brandExpr . ' AS brand', 'vb.slug AS brand_slug', $modelExpr . ' AS model', 'vm.slug AS model_slug'];
        foreach (['name_fa', 'name_en', 'slug', 'year_from', 'year_to', 'engine_type', 'body_type'] as $c) {
            if (isset($modelCols[$c])) {
                $selectCols[] = 'vm.' . $c;
            }
        }
        $stmt = $this->db->prepare('SELECT ' . implode(', ', $selectCols) . $from . ' ORDER BY brand, model LIMIT ? OFFSET ?');
        foreach ($params as $index => $param) {
            $stmt->bindValue($index + 1, $param, PDO::PARAM_STR);
        }
        $stmt->bindValue(count($params) + 1, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(count($params) + 2, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return ['vehicles' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'total' => $total, 'page' => $page, 'pages' => max(1, (int) ceil($total / $perPage))];
    }

    public function getVehicle($brand, $model = null, $year = null)
    {
        $brand = trim((string) $brand);
        $model = trim((string) $model);
        if ($brand === '') {
            return false;
        }
        $brandExpr = $this->getBrandNameExpression('vb');
        $modelExpr = $this->getModelNameExpression('vm');

        // Determine available brand/model columns to avoid referencing missing fields
        $brandCols = $this->getTableColumns('vehicle_brands');
        $modelCols = $this->getTableColumns('vehicle_models');
        $brandCol = $this->chooseColumn($brandCols, ['name_fa', 'name_en', 'name', 'slug'], 'slug');
        $modelCol = $this->chooseColumn($modelCols, ['name_fa', 'name_en', 'common_name_fa', 'common_name_en', 'name', 'slug'], 'slug');

        $where = ['vm.status = 1', 'vb.status = 1', '(LOWER(' . $brandExpr . ') = LOWER(?) OR LOWER(vb.`' . $brandCol . '`) = LOWER(?))'];
        $params = [$brand, $brand];
        if ($model !== '') {
            $where[] = '(LOWER(vm.`' . $modelCol . '`) = LOWER(?) OR LOWER(vm.`' . $modelCol . '`) = LOWER(?) OR LOWER(vm.slug) = LOWER(?) OR LOWER(' . $modelExpr . ') = LOWER(?))';
            array_push($params, $model, $model, $model, $model);
        }
        if ($year !== null && ctype_digit((string) $year)) {
            $where[] = '(vm.year_from IS NULL OR vm.year_from <= ?) AND (vm.year_to IS NULL OR vm.year_to >= ?)';
            $params[] = (int) $year;
            $params[] = (int) $year;
        }
        // Build select list safely
        $select = ['vm.*', $brandExpr . ' AS brand', 'vb.slug AS brand_slug', 'vb.country', 'vb.status AS brand_status'];
        if (isset($brandCols['name_fa'])) $select[] = 'vb.name_fa AS brand_name_fa';
        if (isset($brandCols['name_en'])) $select[] = 'vb.name_en AS brand_name_en';

        $stmt = $this->db->prepare('SELECT ' . implode(', ', $select) . ' FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE ' . implode(' AND ', $where) . ' ORDER BY vm.id LIMIT 1');
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function getGeneratedModelData(array $vehicle): array
    {
        $brandSlug = trim((string) ($vehicle['brand_slug'] ?? $vehicle['brand'] ?? ''));
        $modelSlug = trim((string) ($vehicle['model_slug'] ?? $vehicle['slug'] ?? $vehicle['model'] ?? ''));
        if ($brandSlug === '' || $modelSlug === '') {
            return [];
        }

        $path = __DIR__ . '/../Data/generated_models/' . $brandSlug . '/' . $modelSlug . '.php';
        if (!file_exists($path)) {
            return [];
        }

        try {
            $data = include $path;
            return is_array($data) ? $data : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getVehicleServiceMatrixLinks(array $vehicle, int $limit = 6): array
    {
        $brandSlug = trim((string) ($vehicle['brand_slug'] ?? $vehicle['brand'] ?? ''));
        $modelSlug = trim((string) ($vehicle['model_slug'] ?? $vehicle['slug'] ?? $vehicle['model'] ?? ''));
        if ($brandSlug === '' || $modelSlug === '') {
            return [];
        }

        $brandSlug = strtolower(str_replace([' ', '_'], '-', $brandSlug));
        $modelSlug = strtolower(str_replace([' ', '_'], '-', $modelSlug));

        try {
            $stmt = $this->db->prepare('SELECT id, slug, title_fa, title_en, description_fa FROM services WHERE status = 1 ORDER BY id ASC');
            $stmt->execute();
            $services = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }

        $matrixRoot = __DIR__ . '/../Data/generated_matrices';
        $links = [];
        foreach ($services as $serviceRow) {
            $serviceSlug = trim((string) ($serviceRow['slug'] ?? ''));
            if ($serviceSlug === '') {
                continue;
            }

            $matrixPath = $matrixRoot . '/' . $serviceSlug . '/' . $brandSlug . '/' . $modelSlug . '.php';
            if (!file_exists($matrixPath)) {
                continue;
            }

            $subUrl = null;
            try {
                $subModel = new \App\Models\ServiceSubcategory();
                $subs = $subModel->getByService($serviceSlug);
                if (!empty($subs)) {
                    $subUrl = SITE_URL . '/services/' . rawurlencode($serviceSlug) . '/' . rawurlencode((string) ($subs[0]['slug'] ?? ''));
                }
            } catch (\Throwable $e) {
                $subUrl = null;
            }

            $links[] = [
                'slug' => $serviceSlug,
                'title_fa' => $serviceRow['title_fa'] ?? $serviceRow['title_en'] ?? '',
                'service_url' => SITE_URL . '/services/' . rawurlencode($serviceSlug) . '/' . rawurlencode($modelSlug),
                'sub_url' => $subUrl,
            ];
        }

        return array_slice($links, 0, max(1, (int) $limit));
    }

    public function getPopularVehicles($limit = 6)
    {
        $stmt = $this->db->prepare('SELECT vm.id, ' . $this->getBrandNameExpression('vb') . ' AS brand, vb.slug AS brand_slug, ' . $this->getModelNameExpression('vm') . ' AS model, vm.slug AS model_slug, vm.slug, vm.year_from AS year_start, vm.year_to AS year_end, vm.engine_type FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND vb.status = 1 ORDER BY vm.id DESC LIMIT ?');
        $stmt->bindValue(1, max(1, min(20, (int) $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getVehiclesByCategory($category, $limit = 50)
    {
        $category = trim((string) $category);
        // Build a safe select list and where clause for category
        $brandCols = $this->getTableColumns('vehicle_brands');
        $modelCols = $this->getTableColumns('vehicle_models');
        $brandCol = $this->chooseColumn($brandCols, ['name_fa', 'name_en', 'name', 'slug'], 'name');
        $modelSelect = ['vm.id'];
        if (isset($brandCols['name_fa'])) $brandSelect = 'vb.name_fa AS brand'; else $brandSelect = 'vb.' . $brandCol . ' AS brand';
        if (isset($brandCols['name_en'])) $brandEn = 'vb.name_en'; else $brandEn = 'vb.' . $brandCol;
        if (isset($modelCols['name_fa'])) $modelSelect[] = 'vm.name_fa AS model';
        if (isset($modelCols['name_en'])) $modelSelect[] = 'vm.name_en AS model_en';
        if (isset($modelCols['slug'])) $modelSelect[] = 'vm.slug';
        if (isset($modelCols['year_from'])) $modelSelect[] = 'vm.year_from AS year_start';
        if (isset($modelCols['year_to'])) $modelSelect[] = 'vm.year_to AS year_end';
        if (isset($modelCols['engine_type'])) $modelSelect[] = 'vm.engine_type';

        $sql = 'SELECT ' . $brandSelect . ', ' . $brandEn . ' AS brand_en, vb.country, ' . implode(', ', $modelSelect) . ' FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND vb.status = 1 AND (vb.country = ? OR vb.`' . $brandCol . '` = ? OR vb.`' . $brandCol . '` = ?) ORDER BY vm.name_fa LIMIT ?';
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(1, $category, PDO::PARAM_STR);
        $stmt->bindValue(2, $category, PDO::PARAM_STR);
        $stmt->bindValue(3, $category, PDO::PARAM_STR);
        $stmt->bindValue(4, max(1, min(100, (int) $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSymptomsForVehicle($modelId, $limit = 8)
    {
        if (!$this->hasTable('vehicle_symptoms')) return [];
        $columns = $this->getTableColumns('vehicle_symptoms');
        if (!isset($columns['category'])) return [];
        $model = $this->findById((int) $modelId);
        $category = $model['brand_name_fa'] ?? $model['brand'] ?? '';
        $stmt = $this->db->prepare('SELECT id, name, description, category FROM vehicle_symptoms WHERE category = ? ORDER BY id DESC LIMIT ?');
        $stmt->bindValue(1, $category, PDO::PARAM_STR);
        $stmt->bindValue(2, max(1, min(20, (int) $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRelatedArticles($model, $limit = 4)
    {
        $term = trim((string) ($model['name_fa'] ?? $model['model'] ?? ''));
        if ($term === '') return [];
        $stmt = $this->db->prepare('SELECT id, title_fa, slug, category, meta_description_fa FROM articles WHERE status = 1 AND (title_fa LIKE ? OR content_fa LIKE ? OR search_keywords_fa LIKE ?) ORDER BY created_at DESC LIMIT ?');
        $like = '%' . $term . '%';
        $stmt->bindValue(1, $like, PDO::PARAM_STR);
        $stmt->bindValue(2, $like, PDO::PARAM_STR);
        $stmt->bindValue(3, $like, PDO::PARAM_STR);
        $stmt->bindValue(4, max(1, min(10, (int) $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecommendedServices($limit = 5)
    {
        $stmt = $this->db->prepare("SELECT id, title_fa, slug, description_fa, seo_description_fa FROM services WHERE status = 1 AND (title_fa LIKE '%دیاگ%' OR title_fa LIKE '%برق%' OR title_fa LIKE '%موتور%' OR title_fa LIKE '%گیربکس%' OR title_fa LIKE '%کولر%') ORDER BY id ASC LIMIT ?");
        $stmt->bindValue(1, max(1, min(10, (int) $limit)), PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getBrands()
    {
        if (!$this->hasTable('vehicle_models') || !$this->hasTable('vehicle_brands')) {
            return [];
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $stmt = $this->db->prepare('SELECT DISTINCT ' . $brandExpr . ' AS brand FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 ORDER BY brand');
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'brand');
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getModels($brand = null)
    {
        if (!$this->hasTable('vehicle_models') || !$this->hasTable('vehicle_brands')) {
            return [];
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $modelExpr = $this->getModelNameExpression('vm');
            $sql = 'SELECT DISTINCT ' . $modelExpr . ' AS model FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1';
            $params = [];

            if ($brand) {
                $sql .= ' AND LOWER(' . $brandExpr . ') = LOWER(?)';
                $params[] = $brand;
            }

            $sql .= ' ORDER BY model';
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'model');
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getYears($brand = null, $model = null)
    {
        if (!$this->hasTable('vehicle_models') || !$this->hasTable('vehicle_brands')) {
            return [];
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $modelExpr = $this->getModelNameExpression('vm');
            $sql = 'SELECT DISTINCT vm.year_from AS year_start, vm.year_to AS year_end FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1';
            $params = [];

            if ($brand) {
                $sql .= ' AND LOWER(' . $brandExpr . ') = LOWER(?)';
                $params[] = $brand;
            }

            if ($model) {
                $sql .= ' AND LOWER(' . $modelExpr . ') = LOWER(?)';
                $params[] = $model;
            }

            $sql .= ' ORDER BY year_start, year_end';
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $years = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $yearStart = (int) ($row['year_start'] ?? 0);
                $yearEnd = (int) ($row['year_end'] ?? 0);
                if ($yearStart > 0 && $yearEnd > 0 && $yearStart === $yearEnd) {
                    $years[] = (string) $yearStart;
                } elseif ($yearStart > 0 && $yearEnd > 0) {
                    $years[] = $yearStart . ' - ' . $yearEnd;
                }
            }

            return array_values(array_unique($years));
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getEngines($brand = null, $model = null)
    {
        if (!$this->hasTable('vehicle_models') || !$this->hasTable('vehicle_brands')) {
            return [];
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $modelExpr = $this->getModelNameExpression('vm');
            $sql = 'SELECT DISTINCT vm.engine_type FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND COALESCE(vm.engine_type, \'\') <> \'\'';
            $params = [];

            if ($brand) {
                $sql .= ' AND LOWER(' . $brandExpr . ') = LOWER(?)';
                $params[] = $brand;
            }

            if ($model) {
                $sql .= ' AND LOWER(' . $modelExpr . ') = LOWER(?)';
                $params[] = $model;
            }

            $sql .= ' ORDER BY vm.engine_type';
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);

            $engines = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'engine_type');
            $engines = array_filter(array_values(array_unique(array_filter($engines))));
            return $engines;
        } catch (\PDOException $e) {
            return [];
        }
    }

    /**
     * Resolve a public vehicle route to its brand, model, and active variant.
     *
     * The model-level fields remain at the top level for compatibility with
     * the existing detail view. Variant fields are also exposed under the
     * `variant` key and with `variant_*` aliases for callers that need them.
     */
    public function findVehicleVariant($brandSlug, $modelSlug, $variantSlug)
    {
        $brandSlug = trim((string) $brandSlug);
        $modelSlug = trim((string) $modelSlug);
        $variantSlug = trim((string) $variantSlug);
        if ($brandSlug === '' || $modelSlug === '' || $variantSlug === '') {
            return false;
        }

        try {
            $variantModel = $this->getVariantModel();
            if (!$variantModel) {
                return false;
            }

            $row = $variantModel->findForVehicleRoute($brandSlug, $modelSlug, $variantSlug);
            if (!$row) {
                return false;
            }

            $variant = $this->normalizeVariantRow($row);
            $row['variant'] = $variant;
            foreach ($variant as $key => $value) {
                $row['variant_' . $key] = $value;
            }

            // Keep the aliases expected by the existing model-level view.
            $row['brand'] = $row['brand'] ?? ($row['brand_name_fa'] ?? ($row['brand_name_en'] ?? $brandSlug));
            $row['model'] = $row['model'] ?? ($row['name_fa'] ?? ($row['name_en'] ?? $modelSlug));
            $row['model_slug'] = $row['model_slug'] ?? ($row['slug'] ?? $modelSlug);

            return $row;
        } catch (\Throwable $e) {
            // A not-yet-applied migration must not break model-level pages.
            return false;
        }
    }

    /**
     * Backward-compatible alias for callers that use a get* naming convention.
     */
    public function getVehicleVariant($brandSlug, $modelSlug, $variantSlug)
    {
        return $this->findVehicleVariant($brandSlug, $modelSlug, $variantSlug);
    }

    /**
     * Return active variants for a model for use by public selector pages.
     */
    public function getActiveVariantsByModelId($modelId): array
    {
        try {
            $variantModel = $this->getVariantModel();
            return $variantModel ? $variantModel->getActiveByModelId((int) $modelId) : [];
        } catch (\Throwable $e) {
            return [];
        }
    }

    private function getVariantModel()
    {
        if ($this->variantModel !== null) {
            return $this->variantModel;
        }

        try {
            $this->variantModel = new VehicleVariant();
        } catch (\Throwable $e) {
            $this->variantModel = false;
        }

        return $this->variantModel ?: null;
    }

    private function normalizeVariantRow(array $row): array
    {
        $fields = [
            'id',
            'model_id',
            'name_fa',
            'name_en',
            'slug',
            'engine_code',
            'engine_type',
            'fuel_type',
            'transmission',
            'year_from',
            'year_to',
            'description_fa',
            'description_en',
            'seo_title_fa',
            'seo_description_fa',
            'search_keywords_fa',
            'status',
            'created_at',
            'updated_at',
        ];

        $variant = [];
        foreach ($fields as $field) {
            $alias = 'variant_' . $field;
            if (array_key_exists($alias, $row)) {
                $variant[$field] = $row[$alias];
            } elseif (array_key_exists($field, $row)) {
                $variant[$field] = $row[$field];
            } else {
                $variant[$field] = null;
            }
        }

        if (isset($variant['id'])) {
            $variant['id'] = (int) $variant['id'];
        }
        if (isset($variant['model_id'])) {
            $variant['model_id'] = (int) $variant['model_id'];
        }

        return $variant;
    }

    public function findById($id)
    {
        if (!$this->hasTable('vehicle_models') || !$this->hasTable('vehicle_brands')) {
            return false;
        }

        try {
            $brandExpr = $this->getBrandNameExpression('vb');
            $stmt = $this->db->prepare('SELECT vm.*, ' . $brandExpr . ' AS brand FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.id = ? LIMIT 1');
            $stmt->execute([(int) $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return false;
        }
    }
}

class Vehicle extends Model
{
    public function saveVehicle($data)
    {
        return true;
    }
}

<?php

namespace App\Models;

use PDO;

class VehicleCatalog extends Model
{
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

        $sql = 'SELECT vm.id, ' . $brandExpr . ' AS brand, ' . $modelExpr . ' AS model, vm.`' . $modelCol . '` AS slug, vm.year_from AS year_start, vm.year_to AS year_end, vm.engine_type, vm.body_type, vm.status FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND (LOWER(' . $brandExpr . ') = LOWER(?) OR LOWER(vb.`' . $brandCol . '`) = LOWER(?)) ORDER BY model';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$brand, $brand]);
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
        $selectCols = ['vm.id', 'vm.brand_id', $brandExpr . ' AS brand', $modelExpr . ' AS model'];
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
        $select = ['vm.*', $brandExpr . ' AS brand', 'vb.country', 'vb.status AS brand_status'];
        if (isset($brandCols['name_fa'])) $select[] = 'vb.name_fa AS brand_name_fa';
        if (isset($brandCols['name_en'])) $select[] = 'vb.name_en AS brand_name_en';

        $stmt = $this->db->prepare('SELECT ' . implode(', ', $select) . ' FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE ' . implode(' AND ', $where) . ' ORDER BY vm.id LIMIT 1');
        $stmt->execute($params);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: false;
    }

    public function getPopularVehicles($limit = 6)
    {
        $stmt = $this->db->prepare('SELECT vm.id, ' . $this->getBrandNameExpression('vb') . ' AS brand, ' . $this->getModelNameExpression('vm') . ' AS model, vm.slug, vm.year_from AS year_start, vm.year_to AS year_end, vm.engine_type FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND vb.status = 1 ORDER BY vm.id DESC LIMIT ?');
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

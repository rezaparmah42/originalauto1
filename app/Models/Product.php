<?php

namespace App\Models;

use PDO;

class Product extends Model
{
    private function getColumnNames(string $table): array
    {
        static $cache = [];

        if (isset($cache[$table])) {
            return $cache[$table];
        }

        $cache[$table] = [];

        try {
            $stmt = $this->db->query('SHOW COLUMNS FROM `' . str_replace('`', '', $table) . '`');
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
                $cache[$table][$column['Field']] = true;
            }
        } catch (\Throwable $e) {
            $cache[$table] = [];
        }

        return $cache[$table];
    }

    private function resolveColumn(array $columns, array $candidates, string $fallback = ''): string
    {
        foreach ($candidates as $candidate) {
            if (isset($columns[$candidate])) {
                return $candidate;
            }
        }

        return $fallback;
    }

    private function normalizeProductRow(array $row): array
    {
        $columns = $this->getColumnNames('products');

        if (!isset($row['title_fa']) && isset($row['title'])) {
            $row['title_fa'] = $row['title'];
        }
        if (!isset($row['title_en']) && isset($row['title_en'])) {
            $row['title_en'] = $row['title_en'] ?? '';
        }
        if (!isset($row['description_fa']) && isset($row['description'])) {
            $row['description_fa'] = $row['description'];
        }
        if (!isset($row['description_en']) && !isset($row['description_fa'])) {
            $row['description_fa'] = $row['description_en'] ?? '';
        }

        if (empty($row['title_fa']) && !empty($row['title'])) {
            $row['title_fa'] = $row['title'];
        }
        if (empty($row['description_fa']) && !empty($row['description'])) {
            $row['description_fa'] = $row['description'];
        }
        if (empty($row['slug']) && !empty($row['slug'])) {
            $row['slug'] = $row['slug'];
        }

        $titleCol = $this->resolveColumn($columns, ['title_fa', 'title', 'title_en'], 'title');
        $descriptionCol = $this->resolveColumn($columns, ['description_fa', 'description', 'description_en'], 'description');

        if (!isset($row['title_fa']) && $titleCol !== '') {
            $row['title_fa'] = $row[$titleCol] ?? '';
        }
        if (!isset($row['description_fa']) && $descriptionCol !== '') {
            $row['description_fa'] = $row[$descriptionCol] ?? '';
        }
        if (!isset($row['title_en'])) {
            $row['title_en'] = $row['title_fa'] ?? '';
        }
        if (!isset($row['description_en'])) {
            $row['description_en'] = $row['description_fa'] ?? '';
        }

        return $row;
    }

    public function findBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE slug = ? AND status = 1 LIMIT 1');
            $stmt->execute([trim((string) $slug)]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->normalizeProductRow($row) : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function findById($id)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE id = ? LIMIT 1');
            $stmt->execute([(int) $id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->normalizeProductRow($row) : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getAll()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM products ORDER BY created_at DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $index => $row) {
                $rows[$index] = $this->normalizeProductRow($row);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getVisibleProducts($page = 1, $perPage = 12, $filters = [])
    {
        try {
            $page = max(1, (int) $page);
            $perPage = max(1, (int) $perPage);
            $offset = ($page - 1) * $perPage;

            $sql = 'SELECT p.* FROM products p WHERE p.status = 1';
            $params = [];
            $term = trim((string) ($filters['q'] ?? ''));
            $categoryId = isset($filters['category']) && $filters['category'] !== '' ? (int) $filters['category'] : null;
            $sort = trim((string) ($filters['sort'] ?? 'newest'));
            $vehicleModelId = isset($filters['vehicle_model_id']) && $filters['vehicle_model_id'] !== '' ? (int) $filters['vehicle_model_id'] : null;
            $vehicleBrandId = isset($filters['vehicle_brand_id']) && $filters['vehicle_brand_id'] !== '' ? (int) $filters['vehicle_brand_id'] : null;
            $vehicleYear = trim((string) ($filters['vehicle_year'] ?? ''));

            if ($term !== '') {
                $sql .= ' AND (p.title_fa LIKE ? OR p.title_en LIKE ? OR p.slug LIKE ? OR p.description_fa LIKE ? OR p.search_keywords_fa LIKE ?)';
                $like = '%' . $term . '%';
                $params = array_merge($params, [$like, $like, $like, $like, $like]);
            }

            if ($categoryId !== null) {
                $sql .= ' AND p.category_id = ?';
                $params[] = $categoryId;
            }

            if ($vehicleModelId !== null && $vehicleModelId > 0) {
                $sql .= ' AND EXISTS (SELECT 1 FROM product_compatibility pc WHERE pc.product_id = p.id AND pc.model_id = ?)';
                $params[] = $vehicleModelId;

                if ($vehicleYear !== '') {
                    $sql .= ' AND EXISTS (SELECT 1 FROM product_compatibility pc2 WHERE pc2.product_id = p.id AND pc2.model_id = ? AND (pc2.year IS NULL OR pc2.year = ?))';
                    $params[] = $vehicleModelId;
                    $params[] = $vehicleYear;
                }
            } elseif ($vehicleBrandId !== null && $vehicleBrandId > 0) {
                $sql .= ' AND EXISTS (SELECT 1 FROM product_compatibility pc INNER JOIN vehicle_models vm ON vm.id = pc.model_id WHERE pc.product_id = p.id AND vm.brand_id = ?)';
                $params[] = $vehicleBrandId;

                if ($vehicleYear !== '') {
                    $sql .= ' AND EXISTS (SELECT 1 FROM product_compatibility pc2 INNER JOIN vehicle_models vm2 ON vm2.id = pc2.model_id WHERE pc2.product_id = p.id AND vm2.brand_id = ? AND (pc2.year IS NULL OR pc2.year = ?))';
                    $params[] = $vehicleBrandId;
                    $params[] = $vehicleYear;
                }
            }

            switch ($sort) {
                case 'price_low':
                    $sql .= ' ORDER BY p.price ASC, p.created_at DESC';
                    break;
                case 'price_high':
                    $sql .= ' ORDER BY p.price DESC, p.created_at DESC';
                    break;
                case 'popular':
                    $sql .= ' ORDER BY p.views_count DESC, p.created_at DESC';
                    break;
                case 'newest':
                default:
                    $sql .= ' ORDER BY p.created_at DESC';
                    break;
            }

            $stmt = $this->db->prepare($sql . ' LIMIT ' . $offset . ', ' . $perPage);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $index => $row) {
                $rows[$index] = $this->normalizeProductRow($row);
            }

            $countStmt = $this->db->prepare(str_replace('SELECT p.*', 'SELECT COUNT(*)', $sql));
            $countStmt->execute($params);
            $total = (int) $countStmt->fetchColumn();

            return ['products' => $rows, 'total' => $total];
        } catch (\PDOException $e) {
            return ['products' => [], 'total' => 0];
        }
    }

    public function incrementViews($productId)
    {
        try {
            $stmt = $this->db->prepare('UPDATE products SET views_count = COALESCE(views_count, 0) + 1 WHERE id = ?');
            return $stmt->execute([(int) $productId]);
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getAllAdmin($page = 1, $perPage = 10, $search = '', $status = null, $categoryId = null, $stockFilter = null)
    {
        try {
            $page = max(1, (int) $page);
            $perPage = max(1, (int) $perPage);
            $offset = ($page - 1) * $perPage;

            $stmt = $this->db->prepare('SELECT * FROM products ORDER BY created_at DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filtered = [];
            foreach ($rows as $row) {
                $row = $this->normalizeProductRow($row);
                $term = trim((string) $search);
                if ($term !== '') {
                    $haystack = strtolower(($row['title_fa'] ?? '') . ' ' . ($row['title_en'] ?? '') . ' ' . ($row['slug'] ?? ''));
                    if (strpos($haystack, strtolower($term)) === false) {
                        continue;
                    }
                }

                if ($status !== null && $status !== '' && (int) $row['status'] !== (int) $status) {
                    continue;
                }

                if ($categoryId !== null && $categoryId !== '' && (int) ($row['category_id'] ?? 0) !== (int) $categoryId) {
                    continue;
                }

                if ($stockFilter !== null && $stockFilter !== '' && $stockFilter !== 'all') {
                    $stock = (int) ($row['stock'] ?? 0);
                    if ($stockFilter === 'in_stock' && $stock <= 0) {
                        continue;
                    }
                    if ($stockFilter === 'out_of_stock' && $stock > 0) {
                        continue;
                    }
                    if ($stockFilter === 'low_stock' && $stock > 10) {
                        continue;
                    }
                }

                $filtered[] = $row;
            }

            $total = count($filtered);
            $slice = array_slice($filtered, $offset, $perPage);

            return [
                'products' => $slice,
                'total' => (int) $total,
            ];
        } catch (\PDOException $e) {
            return ['products' => [], 'total' => 0];
        }
    }

    public function searchParts($filters)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM products p WHERE p.status = 1 ORDER BY p.created_at DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $term = strtolower(trim((string) ($filters['query'] ?? '')));

            if ($term === '') {
                foreach ($rows as $index => $row) {
                    $rows[$index] = $this->normalizeProductRow($row);
                }
                return $rows;
            }

            $filtered = [];
            foreach ($rows as $row) {
                $row = $this->normalizeProductRow($row);
                $haystack = strtolower(($row['title_fa'] ?? '') . ' ' . ($row['title_en'] ?? '') . ' ' . ($row['description_fa'] ?? '') . ' ' . ($row['description_en'] ?? '') . ' ' . ($row['slug'] ?? ''));
                if (strpos($haystack, $term) !== false) {
                    $filtered[] = $row;
                }
            }

            return $filtered;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function smartSearch($term, array $filters = [], $page = 1, $perPage = 12)
    {
        $term = trim((string) $term);
        $vehicleModelId = isset($filters['vehicle_model_id']) && $filters['vehicle_model_id'] !== '' ? (int) $filters['vehicle_model_id'] : null;
        $vehicleBrandId = isset($filters['vehicle_brand_id']) && $filters['vehicle_brand_id'] !== '' ? (int) $filters['vehicle_brand_id'] : null;
        $vehicleYear = trim((string) ($filters['vehicle_year'] ?? ''));
        $categoryId = isset($filters['category']) && $filters['category'] !== '' ? (int) $filters['category'] : null;
        $sort = trim((string) ($filters['sort'] ?? 'relevance'));

        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);

        $products = [];
        try {
            $stmt = $this->db->prepare('SELECT p.* FROM products p WHERE p.status = 1 ORDER BY p.created_at DESC');
            $stmt->execute();
            $rawRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $columns = $this->getColumnNames('products');
            $categoryColumns = $this->getColumnNames('product_categories');
            $categoryNames = [];
            $categoryIds = [];

            if (!empty($rawRows)) {
                $catIds = array_unique(array_filter(array_map(static function ($row) { return isset($row['category_id']) ? (int) $row['category_id'] : 0; }, $rawRows)));
                if (!empty($catIds)) {
                    $placeholders = implode(',', array_fill(0, count($catIds), '?'));
                    $catStmt = $this->db->prepare('SELECT id, name_fa, name_en, slug FROM product_categories WHERE id IN (' . $placeholders . ') AND status = 1');
                    $catStmt->execute(array_values($catIds));
                    foreach ($catStmt->fetchAll(PDO::FETCH_ASSOC) as $categoryRow) {
                        $categoryNames[(int) $categoryRow['id']] = trim((string) ($categoryRow['name_fa'] ?? $categoryRow['name_en'] ?? $categoryRow['slug'] ?? ''));
                    }
                }
            }

            foreach ($rawRows as $row) {
                $product = $this->normalizeProductRow($row);
                $text = array_filter([
                    $product['title_fa'] ?? '',
                    $product['title_en'] ?? '',
                    $product['slug'] ?? '',
                    $product['description_fa'] ?? '',
                    $product['description_en'] ?? '',
                    $product['search_keywords_fa'] ?? '',
                    $product['search_keywords_en'] ?? '',
                    $product['sku'] ?? '',
                    $this->resolveColumn($columns, ['technical_code', 'part_code', 'code', 'oem_code', 'oem', 'product_code'], '') !== '' ? ($product[$this->resolveColumn($columns, ['technical_code', 'part_code', 'code', 'oem_code', 'oem', 'product_code'], '')] ?? '') : '',
                    $categoryNames[(int) ($product['category_id'] ?? 0)] ?? '',
                ]);

                $haystack = strtolower(implode(' ', $text));
                $searchTerm = strtolower($term);

                if ($term !== '') {
                    if (strpos($haystack, $searchTerm) === false) {
                        continue;
                    }
                }

                $score = 0;
                if ($term !== '') {
                    foreach (['title_fa', 'title_en', 'slug', 'sku', 'search_keywords_fa', 'search_keywords_en'] as $field) {
                        if (!empty($product[$field])) {
                            $value = strtolower((string) $product[$field]);
                            if ($value === $searchTerm) {
                                $score += 120;
                            } elseif (strpos($value, $searchTerm) === 0) {
                                $score += 80;
                            } elseif (strpos($value, $searchTerm) !== false) {
                                $score += 50;
                            }
                        }
                    }

                    $categoryLabel = $categoryNames[(int) ($product['category_id'] ?? 0)] ?? '';
                    if ($categoryLabel !== '') {
                        $categoryValue = strtolower($categoryLabel);
                        if ($categoryValue === $searchTerm) {
                            $score += 70;
                        } elseif (strpos($categoryValue, $searchTerm) !== false) {
                            $score += 40;
                        }
                    }
                }

                $compatible = false;
                if ($vehicleModelId !== null && $vehicleModelId > 0) {
                    $compatStmt = $this->db->prepare('SELECT 1 FROM product_compatibility WHERE product_id = ? AND model_id = ? LIMIT 1');
                    $compatStmt->execute([(int) $product['id'], $vehicleModelId]);
                    $compatible = $compatStmt->fetchColumn() !== false;
                    if ($compatible) {
                        $score += 250;
                    } elseif ($term !== '') {
                        $score -= 120;
                    }
                }

                if ($categoryId !== null) {
                    if ((int) ($product['category_id'] ?? 0) !== $categoryId) {
                        continue;
                    }
                }

                if ($vehicleYear !== '') {
                    $yearCheckStmt = $this->db->prepare('SELECT 1 FROM product_compatibility WHERE product_id = ? AND model_id = ? AND (year IS NULL OR year = ?) LIMIT 1');
                    $yearCheckStmt->execute([(int) $product['id'], $vehicleModelId ?: 0, $vehicleYear]);
                    if ($vehicleModelId && $yearCheckStmt->fetchColumn() === false) {
                        $score -= 25;
                    }
                }

                if ($term === '') {
                    $score = 0;
                }

                $products[] = ['product' => $product, 'score' => $score];
            }

            usort($products, static function ($a, $b) {
                if ($a['score'] === $b['score']) {
                    return (int) ($b['product']['created_at'] ?? '0000-00-00') <=> (int) ($a['product']['created_at'] ?? '0000-00-00');
                }
                return $b['score'] <=> $a['score'];
            });

            $total = count($products);
            $slice = array_slice($products, ($page - 1) * $perPage, $perPage);
            $rows = array_map(static function ($entry) { return $entry['product']; }, $slice);

            return ['products' => $rows, 'total' => $total, 'page' => $page, 'pages' => max(1, (int) ceil($total / $perPage))];
        } catch (\Throwable $e) {
            return ['products' => [], 'total' => 0, 'page' => $page, 'pages' => 1];
        }
    }

    public function getSearchSuggestions($term, $limit = 5, array $filters = [])
    {
        $term = trim((string) $term);
        if ($term === '') {
            return ['products' => [], 'categories' => [], 'vehicles' => []];
        }

        $like = '%' . $term . '%';
        $products = [];
        $categories = [];
        $vehicles = [];

        try {
            $productStmt = $this->db->prepare('SELECT * FROM products WHERE status = 1 AND (title_fa LIKE ? OR title_en LIKE ? OR slug LIKE ? OR search_keywords_fa LIKE ? OR sku LIKE ?) ORDER BY created_at DESC LIMIT ?');
            $productStmt->execute([$like, $like, $like, $like, $like, (int) $limit]);
            foreach ($productStmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
                $products[] = $this->normalizeProductRow($row);
            }
        } catch (\Throwable $e) {
            $products = [];
        }

        try {
            $catStmt = $this->db->prepare('SELECT id, name_fa, name_en, slug FROM product_categories WHERE status = 1 AND (name_fa LIKE ? OR name_en LIKE ? OR slug LIKE ?) ORDER BY name_fa ASC LIMIT ?');
            $catStmt->execute([$like, $like, $like, (int) $limit]);
            $categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            $categories = [];
        }

        try {
            $vehicleStmt = $this->db->prepare('SELECT vm.id, COALESCE(vb.name_fa, vb.name, vb.name_en, vb.slug) AS brand_name_fa, COALESCE(vb.name_en, vb.name, vb.name_fa, vb.slug) AS brand_name_en, vm.name_fa AS model_name_fa, COALESCE(vm.name_en, vm.name_fa, vm.slug) AS model_name_en, vm.slug FROM vehicle_models vm INNER JOIN vehicle_brands vb ON vb.id = vm.brand_id WHERE vm.status = 1 AND (vm.name_fa LIKE ? OR vm.name_en LIKE ? OR vb.name_fa LIKE ? OR vb.name_en LIKE ? OR vm.slug LIKE ?) ORDER BY brand_name_fa ASC, vm.name_fa ASC LIMIT ?');
            $vehicleStmt->execute([$like, $like, $like, $like, $like, (int) $limit]);
            $vehicles = $vehicleStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            // Fallback to a conservative query that avoids missing brand/model columns
            try {
                $vehicleStmt = $this->db->prepare('SELECT vm.id, vm.name_fa AS model_name_fa, vm.slug FROM vehicle_models vm WHERE vm.status = 1 AND (vm.name_fa LIKE ? OR vm.slug LIKE ?) ORDER BY vm.name_fa ASC LIMIT ?');
                $vehicleStmt->execute([$like, $like, (int) $limit]);
                $vehicles = $vehicleStmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e2) {
                $vehicles = [];
            }
        }

        return ['products' => $products, 'categories' => $categories, 'vehicles' => $vehicles];
    }

    public function search($search = '', $status = null, $page = 1, $perPage = 10)
    {
        return $this->getAllAdmin($page, $perPage, $search, $status);
    }

    public function create($data)
    {
        $columns = [
            'title_fa', 'title_en', 'slug', 'description_fa', 'description_en',
            'seo_title_fa', 'seo_description_fa', 'search_keywords_fa', 'image', 'price',
            'stock', 'sku', 'specifications', 'category_id', 'featured', 'status', 'created_at'
        ];
        $values = [
            $data['title_fa'] ?? $data['title'] ?? '',
            $data['title_en'] ?? '',
            $data['slug'] ?? '',
            $data['description_fa'] ?? $data['description'] ?? '',
            $data['description_en'] ?? '',
            $data['seo_title_fa'] ?? null,
            $data['seo_description_fa'] ?? null,
            $data['search_keywords_fa'] ?? null,
            $data['image'] ?? null,
            $data['price'] ?? 0,
            $data['stock'] ?? 0,
            $data['sku'] ?? null,
            $data['specifications'] ?? null,
            isset($data['category_id']) ? (int) $data['category_id'] : null,
            isset($data['featured']) ? (int) $data['featured'] : 0,
            $data['status'] ?? 1,
            date('Y-m-d H:i:s'),
        ];

        $sql = 'INSERT INTO products (' . implode(', ', $columns) . ') VALUES (' . implode(', ', array_fill(0, count($values), '?')) . ')';
        $stmt = $this->db->prepare($sql);

        if ($stmt->execute($values)) {
            $insertId = $this->db->lastInsertId();
            return $insertId !== false ? (int) $insertId : true;
        }

        return false;
    }

    public function update($id, $data)
    {
        $sql = 'UPDATE products SET title_fa = ?, title_en = ?, slug = ?, description_fa = ?, description_en = ?, seo_title_fa = ?, seo_description_fa = ?, search_keywords_fa = ?, image = ?, price = ?, stock = ?, sku = ?, specifications = ?, category_id = ?, featured = ?, status = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);

        return $stmt->execute([
            $data['title_fa'] ?? $data['title'] ?? '',
            $data['title_en'] ?? '',
            $data['slug'] ?? '',
            $data['description_fa'] ?? $data['description'] ?? '',
            $data['description_en'] ?? '',
            $data['seo_title_fa'] ?? null,
            $data['seo_description_fa'] ?? null,
            $data['search_keywords_fa'] ?? null,
            $data['image'] ?? null,
            $data['price'] ?? 0,
            $data['stock'] ?? 0,
            $data['sku'] ?? null,
            $data['specifications'] ?? null,
            isset($data['category_id']) ? (int) $data['category_id'] : null,
            isset($data['featured']) ? (int) $data['featured'] : 0,
            $data['status'] ?? 1,
            $id,
        ]);
    }

    public function categoriesList()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM product_categories WHERE status = 1 ORDER BY parent_id ASC, name_fa ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getRelatedProducts($productId, $categoryId = null, $limit = 4)
    {
        try {
            $sql = 'SELECT * FROM products WHERE status = 1 AND id <> ?';
            $params = [(int) $productId];
            if ($categoryId) {
                $sql .= ' AND category_id = ?';
                $params[] = (int) $categoryId;
            }
            $sql .= ' ORDER BY created_at DESC LIMIT ?';
            $params[] = (int) $limit;
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $i => $row) {
                $rows[$i] = $this->normalizeProductRow($row);
            }
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getProductImages($productId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order ASC, id ASC');
            $stmt->execute([(int) $productId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $e) {
            try {
                $stmt = $this->db->prepare('SELECT * FROM product_images WHERE product_id = ? ORDER BY display_order ASC, id ASC');
                $stmt->execute([(int) $productId]);
                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $e2) {
                return [];
            }
        }
    }

    public function addProductImage($productId, $imagePath, $displayOrder = 0)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO product_images (product_id, image, display_order, is_primary, created_at) VALUES (?, ?, ?, ?, ?)');
            $primary = 0;
            $count = (int) $this->db->query('SELECT COUNT(*) FROM product_images WHERE product_id = ' . (int) $productId)->fetchColumn();
            if ($count === 0) {
                $primary = 1;
            }
            return $stmt->execute([(int) $productId, $imagePath, (int) $displayOrder, $primary, date('Y-m-d H:i:s')]);
        } catch (\Throwable $e) {
            try {
                $stmt = $this->db->prepare('INSERT INTO product_images (product_id, image, display_order, created_at) VALUES (?, ?, ?, ?)');
                return $stmt->execute([(int) $productId, $imagePath, (int) $displayOrder, date('Y-m-d H:i:s')]);
            } catch (\Throwable $e2) {
                return false;
            }
        }
    }

    public function setPrimaryProductImage($productId, $imageId)
    {
        try {
            $this->db->beginTransaction();
            $this->db->prepare('UPDATE product_images SET is_primary = 0 WHERE product_id = ?')->execute([(int) $productId]);
            $stmt = $this->db->prepare('UPDATE product_images SET is_primary = 1 WHERE product_id = ? AND id = ?');
            $ok = $stmt->execute([(int) $productId, (int) $imageId]);
            $this->db->commit();
            return $ok;
        } catch (\Throwable $e) {
            try { $this->db->rollBack(); } catch (\Throwable $e2) {}
            return false;
        }
    }

    public function deleteProductImage($productId, $imageId)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM product_images WHERE product_id = ? AND id = ?');
            $ok = $stmt->execute([(int) $productId, (int) $imageId]);
            if ($ok) {
                $first = $this->db->prepare('SELECT id FROM product_images WHERE product_id = ? ORDER BY is_primary DESC, display_order ASC, id ASC LIMIT 1');
                $first->execute([(int) $productId]);
                $row = $first->fetch(PDO::FETCH_ASSOC);
                if ($row) {
                    $this->db->prepare('UPDATE product_images SET is_primary = 1 WHERE id = ?')->execute([(int) $row['id']]);
                    $this->db->prepare('UPDATE product_images SET is_primary = 0 WHERE id <> ? AND product_id = ?')->execute([(int) $row['id'], (int) $productId]);
                }
            }
            return $ok;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function getFeatured($limit = 8)
    {
        return $this->getFeaturedProducts($limit);
    }

    public function getFeaturedProducts($limit = 8)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE status = 1 AND featured = 1 ORDER BY created_at DESC LIMIT ?');
            $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $i => $r) { $rows[$i] = $this->normalizeProductRow($r); }
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getRelated($productId, $categoryId = null, $limit = 4)
    {
        return $this->getRelatedProducts((int) $productId, $categoryId ? (int) $categoryId : null, (int) $limit);
    }

    public function getRelatedProductsByCategory($productId, $limit = 4)
    {
        try {
            $stmt = $this->db->prepare('SELECT category_id FROM products WHERE id = ? LIMIT 1');
            $stmt->execute([(int) $productId]);
            $cat = $stmt->fetchColumn();
            if (!$cat) { return []; }

            $stmt = $this->db->prepare('SELECT * FROM products WHERE status = 1 AND category_id = ? AND id <> ? ORDER BY created_at DESC LIMIT ?');
            $stmt->bindValue(1, (int) $cat, PDO::PARAM_INT);
            $stmt->bindValue(2, (int) $productId, PDO::PARAM_INT);
            $stmt->bindValue(3, (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $i => $r) { $rows[$i] = $this->normalizeProductRow($r); }
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function filterByCategory($categoryId, $limit = 12)
    {
        $categoryId = (int) $categoryId;
        if ($categoryId <= 0) {
            return [];
        }

        try {
            $sql = 'SELECT * FROM products WHERE status = 1 AND category_id = ? ORDER BY created_at DESC LIMIT ?';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, $categoryId, PDO::PARAM_INT);
            $stmt->bindValue(2, (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $i => $row) {
                $rows[$i] = $this->normalizeProductRow($row);
            }
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function getPopular($limit = 8)
    {
        try {
            $sql = 'SELECT * FROM products WHERE status = 1 ORDER BY views_count DESC, created_at DESC LIMIT ?';
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $i => $row) {
                $rows[$i] = $this->normalizeProductRow($row);
            }
            return $rows;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM products WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function increaseStock($productId, $amount, $note = '')
    {
        $amount = max(0, (int) $amount);
        if ($amount === 0) {
            return false;
        }

        $product = $this->findById($productId);
        if (!$product) {
            return false;
        }

        $oldStock = (int) $product['stock'];
        $newStock = $oldStock + $amount;
        $adminId = $_SESSION['admin']['id'] ?? null;

        $stmt = $this->db->prepare('UPDATE products SET stock = ? WHERE id = ?');
        $success = $stmt->execute([$newStock, $productId]);

        if ($success) {
            $this->logStockHistory($productId, 'increase', $amount, $note, $oldStock, $newStock, $adminId);
        }

        return $success;
    }

    public function decreaseStock($productId, $amount, $note = '')
    {
        $amount = max(0, (int) $amount);
        if ($amount === 0) {
            return false;
        }

        $product = $this->findById($productId);
        if (!$product) {
            return false;
        }

        $oldStock = (int) $product['stock'];
        $newStock = max(0, $oldStock - $amount);
        $adminId = $_SESSION['admin']['id'] ?? null;

        $stmt = $this->db->prepare('UPDATE products SET stock = ? WHERE id = ?');
        $success = $stmt->execute([$newStock, $productId]);

        if ($success) {
            $actualChange = $oldStock - $newStock;
            $this->logStockHistory($productId, 'decrease', $actualChange, $note, $oldStock, $newStock, $adminId);
        }

        return $success;
    }

    public function adjustStock($productId, $quantity, $note = '')
    {
        $quantity = (int) $quantity;
        if ($quantity === 0) {
            return false;
        }

        if ($quantity > 0) {
            return $this->increaseStock($productId, $quantity, $note);
        }

        return $this->decreaseStock($productId, abs($quantity), $note);
    }

    public function getLowStockProducts($threshold = 10)
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE stock > 0 AND stock <= ? ORDER BY stock ASC');
        $stmt->execute([(int) $threshold]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOutOfStockProducts()
    {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE stock <= 0 ORDER BY created_at DESC');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getStockHistory($limit = 100)
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT ih.*, p.title_fa, p.title_en, u.name AS admin_name
                 FROM inventory_history ih
                 LEFT JOIN products p ON p.id = ih.product_id
                 LEFT JOIN users u ON u.id = ih.admin_id
                 ORDER BY ih.created_at DESC
                 LIMIT ?'
            );
            $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
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
            return [];
        }
    }

    public function getProductsBySupplier($supplierId)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE supplier_id = ? ORDER BY created_at DESC');
            $stmt->execute([(int) $supplierId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function createInventoryTransaction(array $data)
    {
        $orderId = isset($data['order_id']) ? (int) $data['order_id'] : null;
        $productId = isset($data['product_id']) ? (int) $data['product_id'] : 0;
        $changeAmount = isset($data['change_amount']) ? (int) $data['change_amount'] : 0;
        $type = trim((string) ($data['type'] ?? 'order_deduction'));
        $note = trim((string) ($data['note'] ?? ''));
        $oldStock = isset($data['old_stock']) ? (int) $data['old_stock'] : null;
        $newStock = isset($data['new_stock']) ? (int) $data['new_stock'] : null;
        $adminId = isset($data['admin_id']) ? (int) $data['admin_id'] : null;
        $createdAt = date('Y-m-d H:i:s');

        try {
            if ($this->tableExists('inventory_transactions')) {
                $stmt = $this->db->prepare(
                    'INSERT INTO inventory_transactions (order_id, product_id, admin_id, old_stock, new_stock, change_amount, type, note, created_at)
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)'
                );
                return $stmt->execute([
                    $orderId,
                    $productId,
                    $adminId,
                    $oldStock,
                    $newStock,
                    $changeAmount,
                    $type,
                    $note,
                    $createdAt,
                ]);
            }

            $stmt = $this->db->prepare(
                'INSERT INTO inventory_history (product_id, admin_id, old_stock, new_stock, change_amount, type, note, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            return $stmt->execute([
                $productId,
                $adminId,
                $oldStock,
                $newStock,
                $changeAmount,
                $type,
                $note,
                $createdAt,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function hasStockProcessed($orderId)
    {
        $orderId = (int) $orderId;
        if ($orderId <= 0) {
            return true;
        }

        try {
            $itemStmt = $this->db->prepare('SELECT COUNT(*) FROM order_items WHERE order_id = ?');
            $itemStmt->execute([$orderId]);
            $expectedItems = (int) $itemStmt->fetchColumn();
            if ($expectedItems === 0) {
                return true;
            }

            if ($this->tableExists('inventory_transactions')) {
                $stmt = $this->db->prepare('SELECT COUNT(*) FROM inventory_transactions WHERE order_id = ?');
                $stmt->execute([$orderId]);
                return (int) $stmt->fetchColumn() >= $expectedItems;
            }

            $stmt = $this->db->prepare('SELECT COUNT(*) FROM inventory_history WHERE note = ?');
            $stmt->execute(['Order #' . $orderId]);
            return (int) $stmt->fetchColumn() >= $expectedItems;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function processPaidOrderStock($orderId)
    {
        $orderId = (int) $orderId;
        if ($orderId <= 0) {
            return false;
        }

        if ($this->hasStockProcessed($orderId)) {
            return true;
        }

        try {
            $this->db->beginTransaction();

            $itemStmt = $this->db->prepare('SELECT * FROM order_items WHERE order_id = ?');
            $itemStmt->execute([$orderId]);
            $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($items)) {
                $this->db->commit();
                return true;
            }

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = max(1, (int) ($item['quantity'] ?? 1));
                if ($productId <= 0) {
                    continue;
                }

                $product = $this->findById($productId);
                if (!$product) {
                    continue;
                }

                $oldStock = (int) ($product['stock'] ?? 0);
                $newStock = max(0, $oldStock - $quantity);

                $updateStmt = $this->db->prepare('UPDATE products SET stock = ? WHERE id = ?');
                $updateStmt->execute([$newStock, $productId]);

                $this->createInventoryTransaction([
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'change_amount' => -$quantity,
                    'type' => 'order_deduction',
                    'note' => 'Order #' . $orderId,
                    'old_stock' => $oldStock,
                    'new_stock' => $newStock,
                    'admin_id' => null,
                ]);
            }

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            try { $this->db->rollBack(); } catch (\Throwable $ignored) {}
            return false;
        }
    }

    public function logStockHistory($productId, $changeType, $quantity, $note = '', $previousStock = null, $newStock = null, $adminId = null)
    {
        try {
            $stmt = $this->db->prepare(
                'INSERT INTO inventory_history (product_id, admin_id, old_stock, new_stock, change_amount, type, note, created_at)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );

            return $stmt->execute([
                (int) $productId,
                $adminId ? (int) $adminId : null,
                $previousStock,
                $newStock,
                (int) $quantity,
                $changeType,
                trim($note),
                date('Y-m-d H:i:s'),
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    private function tableExists($table)
    {
        try {
            $stmt = $this->db->query('SHOW TABLES LIKE \'' . str_replace('\'', '', $table) . '\'');
            return $stmt->fetchColumn() !== false;
        } catch (\Throwable $e) {
            return false;
        }
    }

    public function createProduct($data)
    {
        return $this->create($data);
    }

    public function updateProduct($id, $data)
    {
        return $this->update($id, $data);
    }
}


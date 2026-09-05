<?php

namespace App\Models;

use PDO;

class ProductCategory extends Model
{
    public function getAll($status = null)
    {
        $sql = 'SELECT * FROM product_categories WHERE 1=1';
        $params = [];

        if ($status !== null && $status !== 'all') {
            $sql .= ' AND status = ?';
            $params[] = (int) $status;
        }

        $sql .= ' ORDER BY parent_id ASC, name_fa ASC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTree()
    {
        $rows = $this->getAll(1);
        $items = [];
        foreach ($rows as $row) {
            $items[(int) ($row['id'] ?? 0)] = $row;
        }

        $tree = [];
        foreach ($rows as $row) {
            $parentId = (int) ($row['parent_id'] ?? 0);
            if ($parentId > 0 && isset($items[$parentId])) {
                $items[$parentId]['children'][] = $row;
            } else {
                $tree[] = $row;
            }
        }

        return $tree;
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM product_categories WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBySlug($slug)
    {
        $stmt = $this->db->prepare('SELECT * FROM product_categories WHERE slug = ? LIMIT 1');
        $stmt->execute([trim((string) $slug)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $sql = 'INSERT INTO product_categories (parent_id, name_fa, name_en, slug, description_fa, description_en, seo_title_fa, seo_description_fa, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : null,
            trim((string) ($data['name_fa'] ?? '')),
            trim((string) ($data['name_en'] ?? '')),
            trim((string) ($data['slug'] ?? '')),
            trim((string) ($data['description_fa'] ?? '')),
            trim((string) ($data['description_en'] ?? '')),
            trim((string) ($data['seo_title_fa'] ?? '')),
            trim((string) ($data['seo_description_fa'] ?? '')),
            isset($data['status']) ? (int) $data['status'] : 1,
            date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, array $data)
    {
        $sql = 'UPDATE product_categories SET parent_id = ?, name_fa = ?, name_en = ?, slug = ?, description_fa = ?, description_en = ?, seo_title_fa = ?, seo_description_fa = ?, status = ? WHERE id = ?';
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            isset($data['parent_id']) && $data['parent_id'] !== '' ? (int) $data['parent_id'] : null,
            trim((string) ($data['name_fa'] ?? '')),
            trim((string) ($data['name_en'] ?? '')),
            trim((string) ($data['slug'] ?? '')),
            trim((string) ($data['description_fa'] ?? '')),
            trim((string) ($data['description_en'] ?? '')),
            trim((string) ($data['seo_title_fa'] ?? '')),
            trim((string) ($data['seo_description_fa'] ?? '')),
            isset($data['status']) ? (int) $data['status'] : 1,
            (int) $id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM product_categories WHERE id = ?');
        return $stmt->execute([(int) $id]);
    }

    public function countProducts($categoryId)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM products WHERE category_id = ?');
        $stmt->execute([(int) $categoryId]);
        return (int) $stmt->fetchColumn();
    }
}

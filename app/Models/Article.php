<?php

namespace App\Models;

use PDO;

class Article extends Model
{
    private function normalizeArticleRow(array $row): array
    {
        if (!isset($row['title_fa']) && isset($row['title'])) {
            $row['title_fa'] = $row['title'];
        }
        if (!isset($row['title_en'])) {
            $row['title_en'] = $row['title_fa'] ?? '';
        }
        if (!isset($row['content_fa']) && isset($row['content'])) {
            $row['content_fa'] = $row['content'];
        }
        if (!isset($row['content_en'])) {
            $row['content_en'] = $row['content_fa'] ?? '';
        }
        if (!isset($row['seo_title_fa']) && isset($row['seo_title'])) {
            $row['seo_title_fa'] = $row['seo_title'];
        }
        if (!isset($row['seo_description_fa']) && isset($row['seo_description'])) {
            $row['seo_description_fa'] = $row['seo_description'];
        }
        if (!isset($row['meta_description_fa']) && isset($row['meta_description'])) {
            $row['meta_description_fa'] = $row['meta_description'];
        }
        return $row;
    }

    public function getVisibleArticles()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM articles WHERE status = 1 ORDER BY created_at DESC LIMIT 20');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $index => $row) {
                $rows[$index] = $this->normalizeArticleRow($row);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getVisibleCategories()
    {
        try {
            $stmt = $this->db->prepare('SELECT DISTINCT category FROM articles WHERE status = 1 AND category <> "" ORDER BY category ASC');
            $stmt->execute();
            return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'category');
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getVisibleArticlesByCategory($category)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM articles WHERE status = 1 AND category = ? ORDER BY created_at DESC');
            $stmt->execute([trim((string) $category)]);
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $index => $row) {
                $rows[$index] = $this->normalizeArticleRow($row);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function findBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM articles WHERE slug = ? AND status = 1 LIMIT 1');
            $stmt->execute([trim((string) $slug)]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->normalizeArticleRow($row) : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function getPaginated($page = 1, $perPage = 10, $search = '', $status = null)
    {
        try {
            $page = max(1, (int) $page);
            $perPage = max(1, (int) $perPage);
            $offset = ($page - 1) * $perPage;

            $stmt = $this->db->prepare('SELECT * FROM articles ORDER BY created_at DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filtered = [];
            foreach ($rows as $row) {
                $row = $this->normalizeArticleRow($row);
                $term = trim((string) $search);
                if ($term !== '') {
                    $haystack = strtolower(($row['title_fa'] ?? '') . ' ' . ($row['title_en'] ?? '') . ' ' . ($row['slug'] ?? '') . ' ' . ($row['category'] ?? '') . ' ' . ($row['author'] ?? ''));
                    if (strpos($haystack, strtolower($term)) === false) {
                        continue;
                    }
                }

                if ($status !== null && $status !== '' && (int) ($row['status'] ?? 1) !== (int) $status) {
                    continue;
                }

                $filtered[] = $row;
            }

            $total = count($filtered);
            $slice = array_slice($filtered, $offset, $perPage);

            return [
                'articles' => $slice,
                'total' => (int) $total,
            ];
        } catch (\PDOException $e) {
            return ['articles' => [], 'total' => 0];
        }
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM articles WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createArticle(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO articles (
                title_fa, title_en, slug, category, author, content_fa, content_en,
                seo_title_fa, seo_title_en, seo_description_fa, seo_description_en,
                meta_description_fa, meta_description_en, image, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)' 
        );

        return $stmt->execute([
            $data['title_fa'] ?? '',
            $data['title_en'] ?? '',
            $data['slug'] ?? '',
            $data['category'] ?? '',
            $data['author'] ?? '',
            $data['content_fa'] ?? '',
            $data['content_en'] ?? '',
            $data['seo_title_fa'] ?? '',
            $data['seo_title_en'] ?? '',
            $data['seo_description_fa'] ?? '',
            $data['seo_description_en'] ?? '',
            $data['meta_description_fa'] ?? '',
            $data['meta_description_en'] ?? '',
            $data['image'] ?? null,
            $data['status'] ?? 1,
            date('Y-m-d H:i:s'),
        ]);
    }

    public function updateArticle($id, array $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE articles SET
                title_fa = ?, title_en = ?, slug = ?, category = ?, author = ?, content_fa = ?, content_en = ?,
                seo_title_fa = ?, seo_title_en = ?, seo_description_fa = ?, seo_description_en = ?,
                meta_description_fa = ?, meta_description_en = ?, image = ?, status = ?
            WHERE id = ?'
        );

        return $stmt->execute([
            $data['title_fa'] ?? '',
            $data['title_en'] ?? '',
            $data['slug'] ?? '',
            $data['category'] ?? '',
            $data['author'] ?? '',
            $data['content_fa'] ?? '',
            $data['content_en'] ?? '',
            $data['seo_title_fa'] ?? '',
            $data['seo_title_en'] ?? '',
            $data['seo_description_fa'] ?? '',
            $data['seo_description_en'] ?? '',
            $data['meta_description_fa'] ?? '',
            $data['meta_description_en'] ?? '',
            $data['image'] ?? null,
            $data['status'] ?? 1,
            $id,
        ]);
    }

    public function deleteArticle($id)
    {
        $stmt = $this->db->prepare('DELETE FROM articles WHERE id = ?');
        return $stmt->execute([(int) $id]);
    }
}

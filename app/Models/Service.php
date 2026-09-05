<?php

namespace App\Models;

use PDO;

class Service extends Model
{
    private function normalizeServiceRow(array $row): array
    {
        if (!isset($row['title_fa']) && isset($row['title'])) {
            $row['title_fa'] = $row['title'];
        }
        if (!isset($row['title_en'])) {
            $row['title_en'] = $row['title_fa'] ?? '';
        }
        if (!isset($row['description_fa']) && isset($row['description'])) {
            $row['description_fa'] = $row['description'];
        }
        if (!isset($row['description_en'])) {
            $row['description_en'] = $row['description_fa'] ?? '';
        }
        if (!isset($row['seo_title_fa']) && isset($row['seo_title'])) {
            $row['seo_title_fa'] = $row['seo_title'];
        }
        if (!isset($row['seo_description_fa']) && isset($row['seo_description'])) {
            $row['seo_description_fa'] = $row['seo_description'];
        }
        return $row;
    }

    public function getVisibleServices()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM services WHERE status = 1 ORDER BY id ASC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $index => $row) {
                $rows[$index] = $this->normalizeServiceRow($row);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getAll()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM services ORDER BY created_at DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $index => $row) {
                $rows[$index] = $this->normalizeServiceRow($row);
            }
            return $rows;
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function getPaginated($page = 1, $perPage = 10, $search = '', $status = null)
    {
        try {
            $page = max(1, (int) $page);
            $perPage = max(1, (int) $perPage);
            $offset = ($page - 1) * $perPage;

            $stmt = $this->db->prepare('SELECT * FROM services ORDER BY created_at DESC');
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $filtered = [];
            foreach ($rows as $row) {
                $row = $this->normalizeServiceRow($row);
                $term = trim((string) $search);
                if ($term !== '') {
                    $haystack = strtolower(($row['title_fa'] ?? '') . ' ' . ($row['title_en'] ?? '') . ' ' . ($row['slug'] ?? ''));
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
                'services' => $slice,
                'total' => (int) $total,
            ];
        } catch (\PDOException $e) {
            return ['services' => [], 'total' => 0];
        }
    }

    public function search($search = '', $status = null, $page = 1, $perPage = 10)
    {
        return $this->getPaginated($page, $perPage, $search, $status);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM services WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findBySlug($slug)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM services WHERE slug = ? AND status = 1 LIMIT 1');
            $stmt->execute([trim((string) $slug)]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            return $row ? $this->normalizeServiceRow($row) : false;
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function createService(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO services (
                title_fa, title_en, slug, description_fa, description_en,
                seo_title_fa, seo_title_en, seo_description_fa, seo_description_en,
                price, duration, image, status, created_at
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $data['title_fa'] ?? '',
            $data['title_en'] ?? '',
            $data['slug'] ?? '',
            $data['description_fa'] ?? '',
            $data['description_en'] ?? '',
            $data['seo_title_fa'] ?? '',
            $data['seo_title_en'] ?? '',
            $data['seo_description_fa'] ?? '',
            $data['seo_description_en'] ?? '',
            $data['price'] ?? 0,
            $data['duration'] ?? '',
            $data['image'] ?? null,
            $data['status'] ?? 1,
            date('Y-m-d H:i:s')
        ]);
    }

    public function updateService($id, array $data)
    {
        $stmt = $this->db->prepare(
            'UPDATE services SET
                title_fa = ?, title_en = ?, slug = ?, description_fa = ?, description_en = ?,
                seo_title_fa = ?, seo_title_en = ?, seo_description_fa = ?, seo_description_en = ?,
                price = ?, duration = ?, image = ?, status = ?
            WHERE id = ?'
        );

        return $stmt->execute([
            $data['title_fa'] ?? '',
            $data['title_en'] ?? '',
            $data['slug'] ?? '',
            $data['description_fa'] ?? '',
            $data['description_en'] ?? '',
            $data['seo_title_fa'] ?? '',
            $data['seo_title_en'] ?? '',
            $data['seo_description_fa'] ?? '',
            $data['seo_description_en'] ?? '',
            $data['price'] ?? 0,
            $data['duration'] ?? '',
            $data['image'] ?? null,
            $data['status'] ?? 1,
            $id
        ]);
    }

    public function deleteService($id)
    {
        $stmt = $this->db->prepare('DELETE FROM services WHERE id = ?');
        return $stmt->execute([$id]);
    }
}

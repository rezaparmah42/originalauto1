<?php

namespace App\Models;

use PDO;

class OBDCode extends Model
{
    public function findCode($code)
    {
        $stmt = $this->db->prepare('SELECT * FROM obd_error_codes WHERE code = ? LIMIT 1');
        $stmt->execute([(string) $code]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function searchCodes($term)
    {
        $like = '%' . trim((string) $term) . '%';
        $stmt = $this->db->prepare('SELECT * FROM obd_error_codes WHERE code LIKE ? OR title_fa LIKE ? OR description_fa LIKE ? ORDER BY code ASC LIMIT 20');
        $stmt->execute([$like, $like, $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSeverity($code)
    {
        $record = $this->findCode($code);
        return $record['severity'] ?? 'unknown';
    }

    public function getSolutions($code)
    {
        $record = $this->findCode($code);
        if (!$record) {
            return [];
        }

        $actions = $record['recommended_actions'] ?? '';
        return array_values(array_filter(array_map('trim', preg_split('/\r\n|\n/', $actions) ?: [])));
    }
}

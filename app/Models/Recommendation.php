<?php

namespace App\Models;

use PDO;

class Recommendation extends Model
{
    public function getByDiagnostic($diagnosticId)
    {
        $stmt = $this->db->prepare('SELECT * FROM repair_recommendations WHERE diagnostic_id = ? ORDER BY priority DESC');
        $stmt->execute([(int)$diagnosticId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare('INSERT INTO repair_recommendations (diagnostic_id, part_name, action, priority) VALUES (?, ?, ?, ?)');
        return $stmt->execute([
            (int) ($data['diagnostic_id'] ?? 0),
            trim((string) ($data['part_name'] ?? '')),
            trim((string) ($data['action'] ?? '')),
            (int) ($data['priority'] ?? 0),
        ]);
    }
}

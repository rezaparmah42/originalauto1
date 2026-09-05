<?php

namespace App\Models;

use PDO;

class FaultKnowledge extends Model
{
    public function findByCode($code)
    {
        $stmt = $this->db->prepare('SELECT * FROM diagnostic_knowledge WHERE dtc_code = ? LIMIT 1');
        $stmt->execute([trim((string)$code)]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare('INSERT INTO diagnostic_knowledge (dtc_code, title, description, severity, possible_causes, recommended_actions, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            trim((string)($data['dtc_code'] ?? '')),
            trim((string)($data['title'] ?? '')),
            trim((string)($data['description'] ?? '')),
            trim((string)($data['severity'] ?? 'unknown')),
            trim((string)($data['possible_causes'] ?? '')),
            trim((string)($data['recommended_actions'] ?? '')),
            date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, array $data)
    {
        $stmt = $this->db->prepare('UPDATE diagnostic_knowledge SET dtc_code=?, title=?, description=?, severity=?, possible_causes=?, recommended_actions=? WHERE id=?');
        return $stmt->execute([
            trim((string)($data['dtc_code'] ?? '')),
            trim((string)($data['title'] ?? '')),
            trim((string)($data['description'] ?? '')),
            trim((string)($data['severity'] ?? 'unknown')),
            trim((string)($data['possible_causes'] ?? '')),
            trim((string)($data['recommended_actions'] ?? '')),
            (int)$id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM diagnostic_knowledge WHERE id = ?');
        return $stmt->execute([(int)$id]);
    }

    public function search($term, $limit = 50)
    {
        $like = '%' . trim((string)$term) . '%';
        $stmt = $this->db->prepare('SELECT * FROM diagnostic_knowledge WHERE dtc_code LIKE ? OR title LIKE ? OR description LIKE ? LIMIT ?');
        $stmt->execute([$like, $like, $like, (int)$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM diagnostic_knowledge WHERE id = ? LIMIT 1');
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

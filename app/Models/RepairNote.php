<?php
namespace App\Models;

use PDO;

class RepairNote extends Model
{
    public function add($repairId, $userId, $note)
    {
        $stmt = $this->db->prepare('INSERT INTO repair_notes (repair_id, user_id, note, created_at) VALUES (?, ?, ?, ?)');
        return $stmt->execute([(int)$repairId, $userId ?? null, $note, date('Y-m-d H:i:s')]);
    }

    public function getByRepair($repairId)
    {
        $stmt = $this->db->prepare('SELECT * FROM repair_notes WHERE repair_id = ? ORDER BY created_at ASC');
        $stmt->execute([(int)$repairId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

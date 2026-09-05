<?php
namespace App\Models;

use PDO;

class RepairUpdate extends Model
{
    public function addUpdate($repairId, $status, $title, $description, $createdBy = null)
    {
        $stmt = $this->db->prepare('INSERT INTO repair_updates (repair_id, status, title, description, created_by, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([(int)$repairId, $status, $title, $description, $createdBy, date('Y-m-d H:i:s')]);
    }

    public function getTimeline($repairId)
    {
        $stmt = $this->db->prepare('SELECT * FROM repair_updates WHERE repair_id = ? ORDER BY created_at ASC');
        $stmt->execute([(int)$repairId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

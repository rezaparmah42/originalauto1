<?php
namespace App\Models;

use PDO;

class WorkshopTask extends Model
{
    public function create(array $data)
    {
        $stmt = $this->db->prepare('INSERT INTO workshop_tasks (repair_id, technician_id, title, description, priority, status, started_at, completed_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $data['repair_id'],
            $data['technician_id'] ?? null,
            $data['title'] ?? '',
            $data['description'] ?? '',
            $data['priority'] ?? 'normal',
            $data['status'] ?? 'pending',
            $data['started_at'] ?? null,
            $data['completed_at'] ?? null,
        ]);
    }

    public function assign($taskId, $technicianId)
    {
        $stmt = $this->db->prepare('UPDATE workshop_tasks SET technician_id = ? WHERE id = ?');
        return $stmt->execute([(int)$technicianId, (int)$taskId]);
    }

    public function updateStatus($taskId, $status)
    {
        $allowed = ['pending','in_progress','paused','completed','cancelled'];
        if (!in_array($status, $allowed, true)) return false;
        $ts = null;
        if ($status === 'in_progress') $ts = date('Y-m-d H:i:s');
        if ($status === 'completed') $ts = date('Y-m-d H:i:s');
        if ($status === 'completed') {
            $stmt = $this->db->prepare('UPDATE workshop_tasks SET status = ?, completed_at = ? WHERE id = ?');
            return $stmt->execute([$status, $ts, (int)$taskId]);
        }
        $stmt = $this->db->prepare('UPDATE workshop_tasks SET status = ?, started_at = ? WHERE id = ?');
        return $stmt->execute([$status, $ts, (int)$taskId]);
    }

    public function getByRepair($repairId)
    {
        $stmt = $this->db->prepare('SELECT * FROM workshop_tasks WHERE repair_id = ? ORDER BY id ASC');
        $stmt->execute([(int)$repairId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByTechnician($technicianId)
    {
        $stmt = $this->db->prepare('SELECT * FROM workshop_tasks WHERE technician_id = ? ORDER BY id DESC');
        $stmt->execute([(int)$technicianId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

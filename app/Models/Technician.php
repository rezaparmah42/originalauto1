<?php
namespace App\Models;

use PDO;

class Technician extends Model
{
    public function all()
    {
        $stmt = $this->db->prepare('SELECT * FROM technicians ORDER BY created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM technicians WHERE id = ? LIMIT 1');
        $stmt->execute([(int)$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(array $data)
    {
        $stmt = $this->db->prepare('INSERT INTO technicians (user_id, name, phone, specialization, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['name'] ?? '',
            $data['phone'] ?? '',
            $data['specialization'] ?? '',
            $data['status'] ?? 'active',
            date('Y-m-d H:i:s'),
        ]);
    }

    public function update($id, array $data)
    {
        $stmt = $this->db->prepare('UPDATE technicians SET user_id=?, name=?, phone=?, specialization=?, status=? WHERE id=?');
        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['name'] ?? '',
            $data['phone'] ?? '',
            $data['specialization'] ?? '',
            $data['status'] ?? 'active',
            (int)$id,
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->db->prepare('DELETE FROM technicians WHERE id = ?');
        return $stmt->execute([(int)$id]);
    }

    public function getTasks($technicianId)
    {
        $stmt = $this->db->prepare('SELECT * FROM workshop_tasks WHERE technician_id = ? ORDER BY id DESC');
        $stmt->execute([(int)$technicianId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPerformance($technicianId)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) AS total, SUM(status = "completed") AS completed FROM workshop_tasks WHERE technician_id = ?');
        $stmt->execute([(int)$technicianId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $total = (int)($row['total'] ?? 0);
        $completed = (int)($row['completed'] ?? 0);
        $score = $total > 0 ? round(($completed / $total) * 100) : 100;
        return ['total' => $total, 'completed' => $completed, 'score' => $score];
    }

    public function logActivity($technicianId, $action, $description = '')
    {
        $stmt = $this->db->prepare('INSERT INTO technician_activity (technician_id, action, description, created_at) VALUES (?, ?, ?, ?)');
        return $stmt->execute([(int)$technicianId, $action, $description, date('Y-m-d H:i:s')]);
    }
}

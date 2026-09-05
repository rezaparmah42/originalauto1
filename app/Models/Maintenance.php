<?php

namespace App\Models;

use PDO;

class Maintenance extends Model
{
    public function createReminder(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO maintenance_records (vehicle_id, title, description, service_date, next_service_date, mileage, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            (int) ($data['vehicle_id'] ?? 0),
            trim((string) ($data['title'] ?? '')),
            trim((string) ($data['description'] ?? '')),
            $data['service_date'] ?? date('Y-m-d'),
            $data['next_service_date'] ?? date('Y-m-d', strtotime('+3 months')),
            (int) ($data['mileage'] ?? 0),
            $data['status'] ?? 'scheduled',
            date('Y-m-d H:i:s'),
        ]);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE maintenance_records SET status = ? WHERE id = ?');
        return $stmt->execute([$status, (int) $id]);
    }

    public function calculateNextServiceDate($serviceDate, $mileage)
    {
        $date = new \DateTime((string) $serviceDate);
        $date->modify('+3 months');

        return $date->format('Y-m-d');
    }

    public function getByVehicle($vehicleId)
    {
        $stmt = $this->db->prepare('SELECT * FROM maintenance_records WHERE vehicle_id = ? ORDER BY service_date DESC');
        $stmt->execute([(int) $vehicleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function classifyReminder(array $record): string
    {
        $status = strtolower((string) ($record['status'] ?? 'scheduled'));
        if ($status === 'completed') {
            return 'completed';
        }

        $today = new \DateTime('today');
        $serviceDate = null;
        if (!empty($record['next_service_date'])) {
            $serviceDate = new \DateTime((string) $record['next_service_date']);
        } elseif (!empty($record['service_date'])) {
            $serviceDate = new \DateTime((string) $record['service_date']);
        }

        if ($serviceDate !== null) {
            $diff = (int) $today->diff($serviceDate)->format('%r%a');
            if ($diff < 0) {
                return 'overdue';
            }
            if ($diff <= 30) {
                return 'due_soon';
            }
        }

        return 'upcoming';
    }

    public function getUpcomingByVehicle($vehicleId)
    {
        $stmt = $this->db->prepare('SELECT * FROM maintenance_records WHERE vehicle_id = ? AND status != ? ORDER BY service_date ASC LIMIT 5');
        $stmt->execute([(int) $vehicleId, 'completed']);
        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($records as &$record) {
            $record['due_state'] = $this->classifyReminder($record);
            $record['status_label'] = $record['status'] ?? 'scheduled';
        }
        return $records;
    }

    public function getUpcomingForCustomer($userId)
    {
        $stmt = $this->db->prepare(
            'SELECT mr.*, v.brand, v.model, v.year FROM maintenance_records mr INNER JOIN vehicles v ON v.id = mr.vehicle_id WHERE v.user_id = ? AND mr.status != ? ORDER BY mr.service_date ASC LIMIT 10'
        );
        $stmt->execute([(int) $userId, 'completed']);

        $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($records as &$record) {
            $record['due_state'] = $this->classifyReminder($record);
            $record['status_label'] = $record['status'] ?? 'scheduled';
        }

        return $records;
    }

    public function getDiagnostics($vehicleId)
    {
        $stmt = $this->db->prepare('SELECT * FROM diagnostic_reports WHERE vehicle_id = ? ORDER BY created_at DESC LIMIT 10');
        $stmt->execute([(int) $vehicleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

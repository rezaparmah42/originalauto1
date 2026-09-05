<?php

namespace App\Models;

use PDO;

class Diagnostic extends Model
{
    public function createSession(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO diagnostic_sessions (vehicle_id, user_id, device_type, connection_status, created_at) VALUES (?, ?, ?, ?, ?)'
        );

        $ok = $stmt->execute([
            (int) ($data['vehicle_id'] ?? 0),
            (int) ($data['user_id'] ?? 0),
            trim((string) ($data['device_type'] ?? 'ELM327')),
            trim((string) ($data['connection_status'] ?? 'pending')),
            date('Y-m-d H:i:s'),
        ]);

        return $ok ? (int) $this->db->lastInsertId() : 0;
    }

    public function updateSessionStatus($sessionId, $status)
    {
        $stmt = $this->db->prepare('UPDATE diagnostic_sessions SET connection_status = ? WHERE id = ?');
        return $stmt->execute([$status, (int) $sessionId]);
    }

    public function saveResult($sessionId, array $data)
    {
        $code = trim((string) ($data['error_code'] ?? ''));
        if ($code === '') {
            return false;
        }

        $codeId = $this->ensureCode($code, $data);
        if (!$codeId) {
            return false;
        }

        $stmt = $this->db->prepare(
            'INSERT INTO diagnostic_results (session_id, error_code_id, raw_data, created_at) VALUES (?, ?, ?, ?)'
        );

        return $stmt->execute([(int) $sessionId, (int) $codeId, trim((string) ($data['raw_data'] ?? '')), date('Y-m-d H:i:s')]);
    }

    public function getVehicleDiagnostics($vehicleId)
    {
        $stmt = $this->db->prepare(
            'SELECT dr.*, ds.vehicle_id, ds.connection_status, ds.created_at AS session_created_at, oc.code, oc.title_fa, oc.severity, oc.recommended_actions FROM diagnostic_results dr LEFT JOIN diagnostic_sessions ds ON ds.id = dr.session_id LEFT JOIN obd_error_codes oc ON oc.id = dr.error_code_id WHERE ds.vehicle_id = ? ORDER BY ds.created_at DESC, dr.created_at DESC'
        );
        $stmt->execute([(int) $vehicleId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLatestDiagnostic($vehicleId)
    {
        $stmt = $this->db->prepare(
            'SELECT dr.*, ds.vehicle_id, ds.connection_status, ds.created_at AS session_created_at, oc.code, oc.title_fa, oc.severity, oc.recommended_actions FROM diagnostic_results dr LEFT JOIN diagnostic_sessions ds ON ds.id = dr.session_id LEFT JOIN obd_error_codes oc ON oc.id = dr.error_code_id WHERE ds.vehicle_id = ? ORDER BY dr.created_at DESC LIMIT 1'
        );
        $stmt->execute([(int) $vehicleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserDiagnostics($userId)
    {
        $stmt = $this->db->prepare(
            'SELECT ds.id AS session_id, ds.vehicle_id, ds.user_id, ds.device_type, ds.connection_status, ds.created_at AS session_created_at, v.brand AS vehicle_brand, v.model AS vehicle_model, v.vin AS vehicle_vin, GROUP_CONCAT(DISTINCT oc.code SEPARATOR ",") AS dtc_codes, GROUP_CONCAT(DISTINCT oc.title_fa SEPARATOR "; ") AS dtc_titles, GROUP_CONCAT(DISTINCT oc.severity SEPARATOR ",") AS severities FROM diagnostic_sessions ds LEFT JOIN vehicles v ON v.id = ds.vehicle_id LEFT JOIN diagnostic_results dr ON dr.session_id = ds.id LEFT JOIN obd_error_codes oc ON oc.id = dr.error_code_id WHERE ds.user_id = ? GROUP BY ds.id ORDER BY ds.created_at DESC'
        );
        $stmt->execute([(int) $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSession($sessionId)
    {
        $stmt = $this->db->prepare('SELECT ds.*, v.model AS vehicle_model, u.name AS customer_name FROM diagnostic_sessions ds LEFT JOIN vehicles v ON v.id = ds.vehicle_id LEFT JOIN users u ON u.id = ds.user_id WHERE ds.id = ? LIMIT 1');
        $stmt->execute([(int) $sessionId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSessionResults($sessionId)
    {
        $stmt = $this->db->prepare(
            'SELECT dr.*, oc.code, oc.title_fa, oc.description_fa, oc.severity, oc.recommended_actions FROM diagnostic_results dr LEFT JOIN obd_error_codes oc ON oc.id = dr.error_code_id WHERE dr.session_id = ? ORDER BY dr.created_at DESC'
        );
        $stmt->execute([(int) $sessionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllSessions($limit = 50)
    {
        $stmt = $this->db->prepare(
            'SELECT ds.*, v.model AS vehicle_model, u.name AS customer_name FROM diagnostic_sessions ds LEFT JOIN vehicles v ON v.id = ds.vehicle_id LEFT JOIN users u ON u.id = ds.user_id ORDER BY ds.created_at DESC LIMIT ?'
        );
        $stmt->execute([(int) $limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getScanCount()
    {
        $stmt = $this->db->query('SELECT COUNT(*) AS total FROM diagnostic_sessions');
        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function getActiveFaultCount()
    {
        $stmt = $this->db->query('SELECT COUNT(DISTINCT dr.session_id) FROM diagnostic_results dr');
        return (int) ($stmt->fetchColumn() ?: 0);
    }

    public function getRecentDiagnostics($limit = 5)
    {
        return $this->getAllSessions((int) $limit);
    }

    private function ensureCode($code, array $data)
    {
        $existing = $this->db->prepare('SELECT id FROM obd_error_codes WHERE code = ? LIMIT 1');
        $existing->execute([$code]);
        $row = $existing->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return (int) $row['id'];
        }

        $insert = $this->db->prepare(
            'INSERT INTO obd_error_codes (code, system, title_fa, description_fa, severity, possible_causes, recommended_actions, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
        );

        $ok = $insert->execute([
            $code,
            trim((string) ($data['system'] ?? 'OBD2')),
            trim((string) ($data['title_fa'] ?? 'کد خطا')),
            trim((string) ($data['description_fa'] ?? '')),
            trim((string) ($data['severity'] ?? 'unknown')),
            trim((string) ($data['possible_causes'] ?? '')),
            trim((string) ($data['recommended_actions'] ?? '')),
            date('Y-m-d H:i:s'),
        ]);

        return $ok ? (int) $this->db->lastInsertId() : 0;
    }
}

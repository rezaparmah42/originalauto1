<?php

namespace App\Models;

use PDO;

class ApiToken extends Model
{
    public function findByToken(string $token)
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.id AS user_id, u.name AS user_name, u.phone AS user_phone, u.email AS user_email, u.role AS user_role
             FROM api_tokens t
             INNER JOIN users u ON u.id = t.user_id
             WHERE t.token = ?
             LIMIT 1'
        );
        $stmt->execute([$token]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createToken(int $userId, string $name = 'Mobile App', int $expiresInDays = 365, string $deviceName = 'Unknown Device', string $platform = 'unknown')
    {
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+' . max(1, $expiresInDays) . ' days'));

        $stmt = $this->db->prepare(
            'INSERT INTO api_tokens (user_id, name, token, created_at, expires_at, revoked) VALUES (?, ?, ?, ?, ?, 0)'
        );

        $success = $stmt->execute([
            $userId,
            $name,
            $token,
            date('Y-m-d H:i:s'),
            $expiresAt,
        ]);

        if (!$success) {
            return null;
        }

        $tokenId = (int) $this->db->lastInsertId();

        try {
            $deviceModel = new ApiDevice();
            $deviceModel->create([
                'user_id' => $userId,
                'token_id' => $tokenId,
                'device_name' => $deviceName,
                'platform' => $platform,
                'last_active' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Throwable $e) {
            // ignore device tracking failures, token is still usable
        }

        return $token;
    }

    public function touchLastUsed(string $token)
    {
        $stmt = $this->db->prepare('UPDATE api_tokens SET last_used_at = ? WHERE token = ?');
        $stmt->execute([date('Y-m-d H:i:s'), $token]);
    }

    public function revokeToken(string $token)
    {
        $stmt = $this->db->prepare('UPDATE api_tokens SET revoked = 1 WHERE token = ?');
        return $stmt->execute([$token]);
    }

    public function revokeTokenById(int $tokenId)
    {
        $stmt = $this->db->prepare('UPDATE api_tokens SET revoked = 1 WHERE id = ?');
        return $stmt->execute([$tokenId]);
    }

    public function revokeAllUserTokens(int $userId)
    {
        $stmt = $this->db->prepare('UPDATE api_tokens SET revoked = 1 WHERE user_id = ?');
        return $stmt->execute([$userId]);
    }

    public function getActiveTokens(int $limit = 100)
    {
        $stmt = $this->db->prepare(
            'SELECT t.*, u.name AS user_name, u.email AS user_email, d.device_name, d.platform, d.last_active
             FROM api_tokens t
             LEFT JOIN users u ON u.id = t.user_id
             LEFT JOIN api_devices d ON d.token_id = t.id
             WHERE t.revoked = 0 AND t.expires_at > NOW()
             ORDER BY t.last_used_at DESC, t.created_at DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listTokens(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR t.name LIKE ?)';
            $q = '%' . $filters['q'] . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'active') {
                $where[] = 't.revoked = 0 AND (t.expires_at IS NULL OR t.expires_at > NOW())';
            } elseif ($filters['status'] === 'revoked') {
                $where[] = 't.revoked = 1';
            }
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT t.*, u.name AS user_name, u.email AS user_email, d.device_name, d.platform, d.last_active
             FROM api_tokens t
             LEFT JOIN users u ON u.id = t.user_id
             LEFT JOIN api_devices d ON d.token_id = t.id
             $whereSql
             ORDER BY t.last_used_at DESC, t.created_at DESC
             LIMIT ? OFFSET ?";

        $stmt = $this->db->prepare($sql);
        $bindIndex = 1;
        foreach ($params as $p) {
            $stmt->bindValue($bindIndex++, $p);
        }
        $stmt->bindValue($bindIndex++, $limit, PDO::PARAM_INT);
        $stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countTokens(array $filters = [])
    {
        $where = [];
        $params = [];

        if (!empty($filters['q'])) {
            $where[] = '(u.name LIKE ? OR u.email LIKE ? OR u.phone LIKE ? OR t.name LIKE ?)';
            $q = '%' . $filters['q'] . '%';
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
            $params[] = $q;
        }

        if (isset($filters['status']) && $filters['status'] !== 'all') {
            if ($filters['status'] === 'active') {
                $where[] = 't.revoked = 0 AND (t.expires_at IS NULL OR t.expires_at > NOW())';
            } elseif ($filters['status'] === 'revoked') {
                $where[] = 't.revoked = 1';
            }
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = "SELECT COUNT(*) as cnt FROM api_tokens t LEFT JOIN users u ON u.id = t.user_id $whereSql";
        $stmt = $this->db->prepare($sql);
        $idx = 1;
        foreach ($params as $p) {
            $stmt->bindValue($idx++, $p);
        }
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int) ($row['cnt'] ?? 0);
    }
}

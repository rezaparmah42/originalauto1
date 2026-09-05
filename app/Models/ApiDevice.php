<?php

namespace App\Models;

use PDO;

class ApiDevice extends Model
{
    public function create(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO api_devices (user_id, token_id, device_name, platform, last_active, created_at) VALUES (?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            (int) ($data['user_id'] ?? 0),
            (int) ($data['token_id'] ?? 0),
            trim((string) ($data['device_name'] ?? 'Unknown Device')),
            trim((string) ($data['platform'] ?? 'unknown')),
            $data['last_active'] ?? date('Y-m-d H:i:s'),
            $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function updateLastActive(int $deviceId)
    {
        $stmt = $this->db->prepare('UPDATE api_devices SET last_active = ? WHERE id = ?');
        return $stmt->execute([date('Y-m-d H:i:s'), $deviceId]);
    }

    public function findByTokenId(int $tokenId)
    {
        $stmt = $this->db->prepare('SELECT * FROM api_devices WHERE token_id = ? LIMIT 1');
        $stmt->execute([$tokenId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listByUser(int $userId, int $limit = 100)
    {
        $stmt = $this->db->prepare('SELECT * FROM api_devices WHERE user_id = ? ORDER BY last_active DESC LIMIT ?');
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

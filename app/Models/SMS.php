<?php
namespace App\Models;

use PDO;

class SMS extends Model
{
    public function log($userId, $phone, $message, $provider = 'local', $status = 'pending')
    {
        $stmt = $this->db->prepare('INSERT INTO sms_logs (user_id, phone, message, provider, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([(int)$userId, $phone, $message, $provider, $status, date('Y-m-d H:i:s')]);
    }

    public function history($userId, $limit = 50)
    {
        $stmt = $this->db->prepare('SELECT * FROM sms_logs WHERE user_id = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([(int)$userId, (int)$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

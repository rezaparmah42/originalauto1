<?php
namespace App\Models;

use PDO;

class ChatMessage extends Model
{
    public function sendMessage($repairId, $senderId, $senderType, $message)
    {
        $stmt = $this->db->prepare('INSERT INTO chat_messages (repair_id, sender_id, sender_type, message, created_at) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([(int)$repairId, $senderId, $senderType, $message, date('Y-m-d H:i:s')]);
    }

    public function getConversation($repairId, $limit = 200)
    {
        $stmt = $this->db->prepare('SELECT * FROM chat_messages WHERE repair_id = ? ORDER BY created_at ASC LIMIT ?');
        $stmt->execute([(int)$repairId, (int)$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

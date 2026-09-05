<?php
namespace App\Models;

use PDO;

class Notification extends Model
{
    public function create($userId, $type, $title, $message)
    {
        $stmt = $this->db->prepare('INSERT INTO notifications (user_id, type, title, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([(int)$userId, $type, $title, $message, 'unread', date('Y-m-d H:i:s')]);
    }

    public function getUserNotifications($userId, $limit = 50)
    {
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ?');
        $stmt->execute([(int)$userId, (int)$limit]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findForUser($id, $userId)
    {
        $stmt = $this->db->prepare('SELECT * FROM notifications WHERE id = ? AND user_id = ? LIMIT 1');
        $stmt->execute([(int)$id, (int)$userId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markRead($id)
    {
        $stmt = $this->db->prepare('UPDATE notifications SET status = ?, read_at = ? WHERE id = ?');
        return $stmt->execute(['read', date('Y-m-d H:i:s'), (int)$id]);
    }

    public function markReadForUser($id, $userId)
    {
        $stmt = $this->db->prepare('UPDATE notifications SET status = ?, read_at = ? WHERE id = ? AND user_id = ?');
        return $stmt->execute(['read', date('Y-m-d H:i:s'), (int)$id, (int)$userId]);
    }

    public function unreadCount($userId)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM notifications WHERE user_id = ? AND status = ?');
        $stmt->execute([(int)$userId, 'unread']);
        return (int)$stmt->fetchColumn();
    }
}


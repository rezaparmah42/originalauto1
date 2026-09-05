<?php

namespace App\Models;

use PDO;

class ApiLog extends Model
{
    public function create(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO api_logs (user_id, endpoint, method, ip_address, response_code, created_at) VALUES (?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $data['user_id'] !== null ? (int) $data['user_id'] : null,
            trim((string) ($data['endpoint'] ?? '')), 
            trim((string) ($data['method'] ?? '')), 
            trim((string) ($data['ip_address'] ?? '')), 
            (int) ($data['response_code'] ?? 0),
            $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function listRecent(int $limit = 100)
    {
        $stmt = $this->db->prepare('SELECT l.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone FROM api_logs l LEFT JOIN users u ON u.id = l.user_id ORDER BY l.created_at DESC LIMIT ?');
        $stmt->bindValue(1, $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function listRecentFiltered(array $filters = [], int $limit = 50, int $offset = 0)
    {
        $where = [];
        $params = [];

        if (!empty($filters['endpoint'])) {
            $where[] = 'l.endpoint LIKE ?';
            $params[] = '%' . $filters['endpoint'] . '%';
        }

        if (!empty($filters['response_code'])) {
            $where[] = 'l.response_code = ?';
            $params[] = (int) $filters['response_code'];
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        $order = 'l.created_at DESC';
        if (!empty($filters['sort']) && $filters['sort'] === 'asc') {
            $order = 'l.created_at ASC';
        }

        $sql = 'SELECT l.*, u.name AS user_name, u.email AS user_email, u.phone AS user_phone FROM api_logs l LEFT JOIN users u ON u.id = l.user_id ' . $whereSql . ' ORDER BY ' . $order . ' LIMIT ? OFFSET ?';
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

    public function countFiltered(array $filters = [])
    {
        $where = [];
        $params = [];

        if (!empty($filters['endpoint'])) {
            $where[] = 'l.endpoint LIKE ?';
            $params[] = '%' . $filters['endpoint'] . '%';
        }

        if (!empty($filters['response_code'])) {
            $where[] = 'l.response_code = ?';
            $params[] = (int) $filters['response_code'];
        }

        $whereSql = '';
        if (!empty($where)) {
            $whereSql = 'WHERE ' . implode(' AND ', $where);
        }

        $sql = 'SELECT COUNT(*) as cnt FROM api_logs l ' . $whereSql;
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

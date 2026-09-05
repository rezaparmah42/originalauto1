<?php

namespace App\Models;

use PDO;

class User extends Model
{
    public function getPaginated($page = 1, $perPage = 20, $search = '')
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;
        $term = '%' . trim((string) $search) . '%';

        $count = $this->db->prepare('SELECT COUNT(*) FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ?');
        $count->execute([$term, $term, $term]);
        $total = (int) $count->fetchColumn();

        $stmt = $this->db->prepare('SELECT id, name, email, phone, role, status, created_at FROM users WHERE name LIKE ? OR email LIKE ? OR phone LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
        $stmt->bindValue(1, $term, PDO::PARAM_STR);
        $stmt->bindValue(2, $term, PDO::PARAM_STR);
        $stmt->bindValue(3, $term, PDO::PARAM_STR);
        $stmt->bindValue(4, $perPage, PDO::PARAM_INT);
        $stmt->bindValue(5, $offset, PDO::PARAM_INT);
        $stmt->execute();

        return ['users' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'total' => $total];
    }

    public function updateAdminUser($id, array $data)
    {
        $stmt = $this->db->prepare('UPDATE users SET name = ?, email = ?, phone = ?, role = ?, status = ? WHERE id = ?');
        return $stmt->execute([
            trim((string) ($data['name'] ?? '')),
            trim((string) ($data['email'] ?? '')),
            trim((string) ($data['phone'] ?? '')),
            $data['role'] ?? 'customer',
            (int) ($data['status'] ?? 1),
            (int) $id,
        ]);
    }

    public function findByPhoneOrEmail($login)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE phone = ? OR email = ? LIMIT 1');
        $stmt->execute([$login, $login]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByPhone($phone)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE phone = ? LIMIT 1');
        $stmt->execute([$phone]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateProfile($id, array $data)
    {
        $stmt = $this->db->prepare('UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?');
        return $stmt->execute([
            trim((string) ($data['name'] ?? '')),
            trim((string) ($data['phone'] ?? '')),
            trim((string) ($data['email'] ?? '')),
            (int) $id,
        ]);
    }

    public function updatePassword($id, $hashedPassword)
    {
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([$hashedPassword, (int) $id]);
    }

    public function createUser($data)
    {
        $stmt = $this->db->prepare('INSERT INTO users (name, phone, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?)');
        return $stmt->execute([
            $data['name'],
            $data['phone'],
            $data['email'],
            $data['password'],
            'customer',
            date('Y-m-d H:i:s'),
        ]);
    }
}

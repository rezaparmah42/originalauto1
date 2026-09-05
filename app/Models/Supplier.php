<?php

namespace App\Models;

use PDO;

class Supplier extends Model
{
    public function all()
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM suppliers ORDER BY name ASC');
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return [];
        }
    }

    public function find($id)
    {
        try {
            $stmt = $this->db->prepare('SELECT * FROM suppliers WHERE id = ? LIMIT 1');
            $stmt->execute([(int) $id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            return null;
        }
    }

    public function create(array $data)
    {
        try {
            $stmt = $this->db->prepare('INSERT INTO suppliers (name, contact, phone, email, notes, created_at) VALUES (?, ?, ?, ?, ?, ?)');
            return $stmt->execute([
                trim($data['name'] ?? ''),
                trim($data['contact'] ?? ''),
                trim($data['phone'] ?? ''),
                trim($data['email'] ?? ''),
                trim($data['notes'] ?? ''),
                date('Y-m-d H:i:s'),
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function update($id, array $data)
    {
        try {
            $stmt = $this->db->prepare('UPDATE suppliers SET name = ?, contact = ?, phone = ?, email = ?, notes = ? WHERE id = ?');
            return $stmt->execute([
                trim($data['name'] ?? ''),
                trim($data['contact'] ?? ''),
                trim($data['phone'] ?? ''),
                trim($data['email'] ?? ''),
                trim($data['notes'] ?? ''),
                (int) $id,
            ]);
        } catch (\PDOException $e) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $stmt = $this->db->prepare('DELETE FROM suppliers WHERE id = ?');
            return $stmt->execute([(int) $id]);
        } catch (\PDOException $e) {
            return false;
        }
    }
}

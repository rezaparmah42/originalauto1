<?php

namespace App\Models;

use PDO;

class Admin extends Model
{
    public function findByLogin($login)
    {
        // Allow lookup by email/phone/name regardless of role. Controller
        // will enforce allowed admin roles explicitly.
        $stmt = $this->db->prepare(
            'SELECT * FROM users WHERE (email = ? OR phone = ? OR name = ?) LIMIT 1'
        );
        $stmt->execute([$login, $login, $login]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updatePassword($id, $hash)
    {
        $stmt = $this->db->prepare('UPDATE users SET password = ? WHERE id = ?');
        return $stmt->execute([$hash, $id]);
    }

    public function getDashboardStats()
    {
        $stats = [
            'users' => 0,
            'products' => 0,
            'services' => 0,
            'bookings' => 0,
            'repairs' => 0,
            'orders' => 0,
            'low_stock_products' => 0,
            'out_of_stock_products' => 0,
            'suppliers' => 0,
        ];

        $queries = [
            'users' => 'SELECT COUNT(*) FROM users',
            'products' => 'SELECT COUNT(*) FROM products',
            'services' => 'SELECT COUNT(*) FROM services',
            'bookings' => 'SELECT COUNT(*) FROM bookings',
            'repairs' => 'SELECT COUNT(*) FROM repairs',
            'low_stock_products' => 'SELECT COUNT(*) FROM products WHERE stock > 0 AND stock <= 10',
            'out_of_stock_products' => 'SELECT COUNT(*) FROM products WHERE stock <= 0',
        ];

        if ($this->tableExists('orders')) {
            $stats['orders'] = $this->countTable('orders');
            $stats['pending_orders'] = $this->countWhere('orders', "status = 'pending'");

            // older schemas use `total` as the amount column; newer code used
            // `total_amount`. Prefer `total` when present and guard counts
            // that rely on optional columns like `payment_status`.
            if ($this->columnExists('orders', 'total')) {
                $stats['revenue'] = $this->sumWhere('orders', "status IN ('completed','shipped','processing','confirmed')", 'total');
            } else {
                $stats['revenue'] = 0;
            }

            if ($this->columnExists('orders', 'payment_status')) {
                $stats['paid_orders'] = $this->countWhere('orders', "payment_status = 'paid'");
                $stats['pending_payments'] = $this->countWhere('orders', "payment_status = 'pending'");
                $stats['failed_payments'] = $this->countWhere('orders', "payment_status = 'failed'");
            } else {
                $stats['paid_orders'] = 0;
                $stats['pending_payments'] = 0;
                $stats['failed_payments'] = 0;
            }
        }

        foreach ($queries as $key => $query) {
            // Determine the table referenced by this simple COUNT query
            // e.g. 'SELECT COUNT(*) FROM products' -> products
            if (preg_match('/FROM\s+`?([a-zA-Z0-9_]+)`?/i', $query, $m)) {
                $tbl = $m[1];
            } else {
                $tbl = null;
            }

            if ($tbl === null || !$this->tableExists($tbl)) {
                $stats[$key] = 0;
                continue;
            }

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            $stats[$key] = (int) $stmt->fetchColumn();
        }

        if ($this->tableExists('orders')) {
            $stats['orders'] = $this->countTable('orders');
        }

        if ($this->tableExists('suppliers')) {
            $stats['suppliers'] = $this->countTable('suppliers');
        }

        return $stats;
    }

    public function getRecentUsers($limit = 10)
    {
        $stmt = $this->db->prepare(
            'SELECT id, name, email, phone, role, created_at FROM users ORDER BY created_at DESC LIMIT :limit'
        );
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentBookings($limit = 10)
    {
        $stmt = $this->db->prepare(
            'SELECT b.id, b.problem, b.status, b.booking_date, b.created_at, u.name AS user_name, s.title_fa AS service_name
            FROM bookings b
            LEFT JOIN users u ON u.id = b.user_id
            LEFT JOIN services s ON s.id = b.service_id
            ORDER BY b.created_at DESC
            LIMIT :limit'
        );
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getRecentOrders($limit = 10)
    {
        if (!$this->tableExists('orders')) {
            return [];
        }

        // Determine which amount column exists (total_amount vs total)
        if ($this->columnExists('orders', 'total_amount')) {
            $amountSelect = 'o.total_amount AS total_amount';
        } elseif ($this->columnExists('orders', 'total')) {
            $amountSelect = 'o.total AS total_amount';
        } else {
            $amountSelect = 'NULL AS total_amount';
        }

        $sql = sprintf(
            'SELECT o.id, o.status, o.created_at, %s, u.name AS user_name
            FROM orders o
            LEFT JOIN users u ON u.id = o.user_id
            ORDER BY o.created_at DESC
            LIMIT :limit',
            $amountSelect
        );

        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function tableExists($table)
    {
        $safeTable = preg_replace('/[^a-zA-Z0-9_]/', '', (string) $table);
        if ($safeTable === '') {
            return false;
        }

        $stmt = $this->db->query("SHOW TABLES LIKE '{$safeTable}'");
        return (bool) $stmt->fetchColumn();
    }

    private function countTable($table)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM ' . $table);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    private function countWhere($table, $where)
    {
        $stmt = $this->db->prepare('SELECT COUNT(*) FROM ' . $table . ' WHERE ' . $where);
        $stmt->execute();
        return (int) $stmt->fetchColumn();
    }

    private function sumWhere($table, $where, $column)
    {
        $col = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
        $tbl = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
        $stmt = $this->db->prepare('SELECT COALESCE(SUM(' . $col . '), 0) FROM ' . $tbl . ' WHERE ' . $where);
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    private function columnExists($table, $column)
    {
        $tbl = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$table);
        $col = preg_replace('/[^a-zA-Z0-9_]/', '', (string)$column);
        if ($tbl === '' || $col === '') {
            return false;
        }

        try {
            $stmt = $this->db->query("SHOW COLUMNS FROM `{$tbl}` LIKE '{$col}'");
            return (bool) ($stmt && $stmt->fetchColumn() !== false);
        } catch (\Exception $e) {
            return false;
        }
    }
}

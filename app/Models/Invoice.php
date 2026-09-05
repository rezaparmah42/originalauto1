<?php

namespace App\Models;

use PDO;

class Invoice extends Model
{
    public function generateInvoice($orderId)
    {
        $order = (new Order())->find((int) $orderId);
        if (!$order) {
            return false;
        }

        $invoiceNumber = 'INV-' . str_pad((int) $orderId, 6, '0', STR_PAD_LEFT);
        $stmt = $this->db->prepare('INSERT INTO invoices (order_id, invoice_code, total, status, created_at) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([
            (int) $orderId,
            $invoiceNumber,
            (float) ($order['total_amount'] ?? 0),
            'issued',
            date('Y-m-d H:i:s'),
        ]);
    }

    public function findByOrderId($orderId)
    {
        $stmt = $this->db->prepare('SELECT i.*, i.invoice_code AS invoice_number, i.total AS amount FROM invoices i WHERE i.order_id = ? LIMIT 1');
        $stmt->execute([(int) $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT i.*, i.invoice_code AS invoice_number, i.total AS amount FROM invoices i WHERE i.id = ? LIMIT 1');
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->db->prepare('SELECT i.*, i.invoice_code AS invoice_number, i.total AS amount, o.user_id, u.name AS customer_name FROM invoices i LEFT JOIN orders o ON o.id = i.order_id LEFT JOIN users u ON u.id = o.user_id ORDER BY i.created_at DESC');
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}

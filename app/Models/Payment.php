<?php

namespace App\Models;

use PDO;

class Payment extends Model
{
    public function createPayment(array $data)
    {
        $columns = ['order_id', 'user_id', 'amount', 'transaction_id', 'status', 'gateway', 'created_at', 'updated_at'];
        $values = [
            (int) ($data['order_id'] ?? 0),
            (int) ($data['user_id'] ?? 0),
            (float) ($data['amount'] ?? 0),
            trim((string) ($data['transaction_id'] ?? '')),
            $data['status'] ?? 'pending',
            trim((string) ($data['gateway'] ?? 'placeholder')),
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
        ];

        if ($this->paymentTableHasColumn('paid_at')) {
            $columns[] = 'paid_at';
            $values[] = null;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $sql = 'INSERT INTO payments (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute($values);

        if (!$ok) {
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    public function findByOrderId($orderId)
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE order_id = ? ORDER BY created_at DESC LIMIT 1');
        $stmt->execute([(int) $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $status = trim((string) $status);
        $paidAt = ($status === 'paid') ? date('Y-m-d H:i:s') : null;

        if ($this->paymentTableHasColumn('paid_at')) {
            $stmt = $this->db->prepare('UPDATE payments SET status = ?, paid_at = ?, updated_at = ? WHERE id = ?');
            return $stmt->execute([$status, $paidAt, date('Y-m-d H:i:s'), (int) $id]);
        }

        $stmt = $this->db->prepare('UPDATE payments SET status = ?, updated_at = ? WHERE id = ?');
        return $stmt->execute([$status, date('Y-m-d H:i:s'), (int) $id]);
    }

    public function buildCallbackToken($orderId, $paymentId, $userId, $amount)
    {
        $payload = implode('|', [
            (int) $orderId,
            (int) $paymentId,
            (int) $userId,
            number_format((float) $amount, 2, '.', ''),
            'mock_gateway',
        ]);

        return hash_hmac('sha256', $payload, defined('APP_KEY') ? APP_KEY : 'originalshargh-local-dev-key');
    }

    public function verifyCallbackToken($orderId, $paymentId, $userId, $amount, $token)
    {
        $expected = $this->buildCallbackToken($orderId, $paymentId, $userId, $amount);
        return hash_equals($expected, (string) $token);
    }

    public function getHistory($userId)
    {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([(int) $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function paymentTableHasColumn($column)
    {
        try {
            $stmt = $this->db->query('SHOW COLUMNS FROM payments');
            $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($columns as $field) {
                if (($field['Field'] ?? '') === $column) {
                    return true;
                }
            }
            return false;
        } catch (\Throwable $e) {
            return false;
        }
    }
}

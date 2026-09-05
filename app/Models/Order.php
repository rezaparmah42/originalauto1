<?php

namespace App\Models;

use PDO;

class Order extends Model
{
    public function createOrder(array $data)
    {
        $columns = [];
        $values = [];
        $fields = $this->getTableColumns('orders');

        $userId = (int) ($data['user_id'] ?? 0);
        $vehicleId = !empty($data['vehicle_id']) ? (int) $data['vehicle_id'] : null;
        $total = (float) ($data['total_amount'] ?? ($data['total'] ?? 0));
        $status = $data['status'] ?? 'pending';
        $paymentStatus = $data['payment_status'] ?? 'unpaid';
        $address = trim((string) ($data['address'] ?? ''));
        $customerName = trim((string) ($data['customer_name'] ?? ''));
        $mobile = trim((string) ($data['mobile'] ?? ''));
        $city = trim((string) ($data['city'] ?? ''));
        $postalCode = trim((string) ($data['postal_code'] ?? ''));
        $brand = trim((string) ($data['brand'] ?? ''));
        $model = trim((string) ($data['model'] ?? ''));
        $year = trim((string) ($data['year'] ?? ''));
        $now = date('Y-m-d H:i:s');

        if (in_array('user_id', $fields, true)) {
            $columns[] = 'user_id';
            $values[] = $userId;
        }
        if (in_array('vehicle_id', $fields, true)) {
            $columns[] = 'vehicle_id';
            $values[] = $vehicleId;
        }
        if (in_array('total_amount', $fields, true)) {
            $columns[] = 'total_amount';
            $values[] = $total;
        } elseif (in_array('total', $fields, true)) {
            $columns[] = 'total';
            $values[] = $total;
        }
        if (in_array('status', $fields, true)) {
            $columns[] = 'status';
            $values[] = $status;
        }
        if (in_array('payment_status', $fields, true)) {
            $columns[] = 'payment_status';
            $values[] = $paymentStatus;
        }
        if (in_array('address', $fields, true)) {
            $columns[] = 'address';
            $values[] = $address;
        }
        if (in_array('customer_name', $fields, true)) {
            $columns[] = 'customer_name';
            $values[] = $customerName;
        }
        if (in_array('mobile', $fields, true)) {
            $columns[] = 'mobile';
            $values[] = $mobile;
        }
        if (in_array('city', $fields, true)) {
            $columns[] = 'city';
            $values[] = $city;
        }
        if (in_array('postal_code', $fields, true)) {
            $columns[] = 'postal_code';
            $values[] = $postalCode;
        }
        if (in_array('brand', $fields, true)) {
            $columns[] = 'brand';
            $values[] = $brand;
        }
        if (in_array('model', $fields, true)) {
            $columns[] = 'model';
            $values[] = $model;
        }
        if (in_array('year', $fields, true)) {
            $columns[] = 'year';
            $values[] = $year;
        }
        if (in_array('created_at', $fields, true)) {
            $columns[] = 'created_at';
            $values[] = $now;
        }
        if (in_array('updated_at', $fields, true)) {
            $columns[] = 'updated_at';
            $values[] = $now;
        }

        if (empty($columns)) {
            return false;
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $sql = 'INSERT INTO orders (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')';
        $stmt = $this->db->prepare($sql);
        $ok = $stmt->execute($values);
        if (!$ok) {
            return false;
        }

        return (int) $this->db->lastInsertId();
    }

    public function addItem(array $data)
    {
        $fields = $this->getTableColumns('order_items');
        $columns = ['order_id', 'product_id', 'quantity'];
        $values = [
            (int) ($data['order_id'] ?? 0),
            (int) ($data['product_id'] ?? 0),
            (int) ($data['quantity'] ?? 0),
        ];

        if (in_array('price_snapshot', $fields, true)) {
            $columns[] = 'price_snapshot';
            $values[] = (float) ($data['price_snapshot'] ?? $data['price'] ?? 0);
        } elseif (in_array('price', $fields, true)) {
            $columns[] = 'price';
            $values[] = (float) ($data['price_snapshot'] ?? $data['price'] ?? 0);
        }

        $placeholders = implode(', ', array_fill(0, count($values), '?'));
        $stmt = $this->db->prepare('INSERT INTO order_items (' . implode(', ', $columns) . ') VALUES (' . $placeholders . ')');
        return $stmt->execute($values);
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC'
        );
        $stmt->execute([(int) $userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function getTableColumns($table)
    {
        try {
            $stmt = $this->db->query('SHOW COLUMNS FROM `' . str_replace('`', '', $table) . '`');
            $columns = [];
            foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $column) {
                $columns[] = $column['Field'];
            }
            return $columns;
        } catch (\Throwable $e) {
            return [];
        }
    }

    public function find($id)
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, u.name AS customer_name, u.phone AS customer_phone, u.email AS customer_email, CONCAT(COALESCE(v.model, ""), IF(v.year IS NULL OR v.year = "", "", CONCAT(" ", v.year))) AS vehicle_name
             FROM orders o
             LEFT JOIN users u ON u.id = o.user_id
             LEFT JOIN vehicles v ON v.id = o.vehicle_id
             WHERE o.id = ? LIMIT 1'
        );
        $stmt->execute([(int) $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getItems($orderId)
    {
        $stmt = $this->db->prepare(
            'SELECT oi.*, p.title_fa, p.title_en, p.slug
             FROM order_items oi
             LEFT JOIN products p ON p.id = oi.product_id
             WHERE oi.order_id = ?'
        );
        $stmt->execute([(int) $orderId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll($search = '', $status = '')
    {
        $sql = 'SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE 1=1';
        $params = [];

        if ($search !== '') {
            $sql .= ' AND (o.id = ? OR u.name LIKE ? OR u.phone LIKE ?)';
            $params[] = (int) $search;
            $params[] = '%' . trim($search) . '%';
            $params[] = '%' . trim($search) . '%';
        }

        if ($status !== '' && $status !== 'all') {
            $sql .= ' AND o.status = ?';
            $params[] = $status;
        }

        $sql .= ' ORDER BY o.created_at DESC';
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function changeStatus($id, $newStatus, $changedBy = null, $note = '')
    {
        $orderId = (int) $id;
        $status = trim((string) $newStatus);
        if ($orderId <= 0 || $status === '') {
            return false;
        }

        $order = $this->find($orderId);
        if (!$order) {
            return false;
        }

        $oldStatus = (string) ($order['status'] ?? 'pending');
        if ($oldStatus === $status) {
            return true;
        }

        $this->db->beginTransaction();
        try {
            $stmt = $this->db->prepare('UPDATE orders SET status = ?, updated_at = ? WHERE id = ?');
            $updated = $stmt->execute([$status, date('Y-m-d H:i:s'), $orderId]);
            if (!$updated) {
                $this->db->rollBack();
                return false;
            }

            $historyStmt = $this->db->prepare(
                'INSERT INTO order_status_history (order_id, old_status, new_status, changed_by, note, created_at) VALUES (?, ?, ?, ?, ?, ?)'
            );
            $historyStmt->execute([
                $orderId,
                $oldStatus,
                $status,
                $changedBy !== null ? trim((string) $changedBy) : null,
                trim((string) $note),
                date('Y-m-d H:i:s'),
            ]);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            try { $this->db->rollBack(); } catch (\Throwable $ignored) {}
            return false;
        }
    }

    public function getStatusHistory($orderId)
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM order_status_history WHERE order_id = ? ORDER BY created_at ASC'
        );
        $stmt->execute([(int) $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerOrders($customerId)
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id WHERE o.user_id = ? ORDER BY o.created_at DESC'
        );
        $stmt->execute([(int) $customerId]);
        $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($orders as $index => $order) {
            $orders[$index]['items'] = $this->getOrderItems((int) ($order['id'] ?? 0));
        }

        return $orders;
    }

    public function getOrderItems($orderId)
    {
        return $this->getItems((int) $orderId);
    }

    public function updateStatus($id, $status)
    {
        return $this->changeStatus((int) $id, (string) $status, null, '');
    }

    public function updatePaymentStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE orders SET payment_status = ? WHERE id = ?');
        return $stmt->execute([$status, (int) $id]);
    }

    public function getStats()
    {
        $stats = [
            'total_orders' => 0,
            'pending_orders' => 0,
            'revenue' => 0,
        ];

        $stmt = $this->db->prepare('SELECT COUNT(*) FROM orders');
        $stmt->execute();
        $stats['total_orders'] = (int) $stmt->fetchColumn();

        $pendingStmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE status = 'pending'");
        $pendingStmt->execute();
        $stats['pending_orders'] = (int) $pendingStmt->fetchColumn();

        $revenueStmt = $this->db->prepare('SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status IN (\'completed\', \'shipped\', \'processing\', \'confirmed\')');
        $revenueStmt->execute();
        $stats['revenue'] = (float) $revenueStmt->fetchColumn();

        return $stats;
    }

    public function getRecentOrders($limit = 5)
    {
        $stmt = $this->db->prepare(
            'SELECT o.*, u.name AS customer_name FROM orders o LEFT JOIN users u ON u.id = o.user_id ORDER BY o.created_at DESC LIMIT ?'
        );
        $stmt->bindValue(1, (int) $limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getPaymentStatusStats()
    {
        $stats = [
            'paid_orders' => 0,
            'pending_payments' => 0,
            'failed_payments' => 0,
        ];

        $paidStmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE payment_status = 'paid'");
        $paidStmt->execute();
        $stats['paid_orders'] = (int) $paidStmt->fetchColumn();

        $pendingStmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE payment_status = 'pending'");
        $pendingStmt->execute();
        $stats['pending_payments'] = (int) $pendingStmt->fetchColumn();

        $failedStmt = $this->db->prepare("SELECT COUNT(*) FROM orders WHERE payment_status = 'failed'");
        $failedStmt->execute();
        $stats['failed_payments'] = (int) $failedStmt->fetchColumn();

        return $stats;
    }

    public function getInvoiceRelation($orderId)
    {
        $stmt = $this->db->prepare('SELECT * FROM invoices WHERE order_id = ? LIMIT 1');
        $stmt->execute([(int) $orderId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isPaid($orderId)
    {
        $order = $this->find((int) $orderId);
        return !empty($order) && (($order['payment_status'] ?? 'pending') === 'paid');
    }
}

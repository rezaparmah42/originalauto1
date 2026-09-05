<?php

namespace App\Models;

use PDO;
use App\Models\Product;

class Repair extends Model
{
    public function createRepair($bookingId, $description, $status = 'pending', $cost = 0)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO repairs (booking_id, diagnosis, repair_notes, cost, status, created_at) VALUES (?, ?, ?, ?, ?, ?)'
        );

        $res = $stmt->execute([
            $bookingId,
            $description,
            $description,
            (float) $cost,
            $status,
            date('Y-m-d H:i:s'),
        ]);

        if ($res) {
            return (int) $this->db->lastInsertId();
        }

        return false;
    }

    public function getRepairs()
    {
        $stmt = $this->db->prepare('SELECT * FROM repairs ORDER BY created_at DESC');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findByBooking($bookingId)
    {
        $stmt = $this->db->prepare('SELECT * FROM repairs WHERE booking_id = ? ORDER BY created_at DESC');
        $stmt->execute([$bookingId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM repairs WHERE id = ? LIMIT 1');
        $stmt->execute([(int) $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function isRepairForUser($repairId, $userId)
    {
        $repair = $this->findById((int) $repairId);
        if (!$repair) {
            return false;
        }

        $bookingId = (int) ($repair['booking_id'] ?? 0);
        if ($bookingId <= 0) {
            return false;
        }

        $stmt = $this->db->prepare('SELECT user_id FROM bookings WHERE id = ? LIMIT 1');
        $stmt->execute([$bookingId]);
        $booking = $stmt->fetch(PDO::FETCH_ASSOC);

        return !empty($booking) && (int) ($booking['user_id'] ?? 0) === (int) $userId;
    }

    public function updateRepairStatus($id, $status)
    {
        if (!$this->validateStatus($status)) {
            return false;
        }

        $current = $this->findById($id);
        if (!$current) {
            return false;
        }

        $stmt = $this->db->prepare('UPDATE repairs SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function getActiveCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM repairs WHERE status NOT IN ('completed', 'cancelled')");
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function findAssignedToMechanic($mechanicId)
    {
        $stmt = $this->db->prepare('SELECT * FROM repairs ORDER BY id DESC');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        return $this->updateRepairStatus($id, $status);
    }

    public function getParts($repairId)
    {
        $stmt = $this->db->prepare('SELECT * FROM repair_parts WHERE repair_id = ? ORDER BY id ASC');
        $stmt->execute([$repairId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerRepairs($customerId)
    {
        $stmt = $this->db->prepare(
            'SELECT r.id, r.booking_id, r.diagnosis, r.repair_notes, r.cost, r.status, r.created_at, b.booking_date, b.problem, b.vehicle_id AS vehicle_id, s.title AS service_title, v.model AS vehicle_model, v.brand AS vehicle_brand, t.name AS technician_name FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id LEFT JOIN services s ON s.id = b.service_id LEFT JOIN vehicles v ON v.id = b.vehicle_id LEFT JOIN technicians t ON t.id = r.technician_id WHERE b.user_id = ? ORDER BY r.created_at DESC'
        );
        $stmt->execute([(int) $customerId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCustomerRepairDetail($customerId, $repairId)
    {
        $stmt = $this->db->prepare(
            'SELECT r.*, b.user_id, b.booking_date, b.problem, b.vehicle_id AS vehicle_id, s.title AS service_title, v.brand AS vehicle_brand, v.model AS vehicle_model, v.year AS vehicle_year, v.engine AS vehicle_engine, v.vin AS vehicle_vin, t.name AS technician_name, t.phone AS technician_phone, t.specialization AS technician_specialization FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id LEFT JOIN services s ON s.id = b.service_id LEFT JOIN vehicles v ON v.id = b.vehicle_id LEFT JOIN technicians t ON t.id = r.technician_id WHERE r.id = ? AND b.user_id = ? LIMIT 1'
        );
        $stmt->execute([(int) $repairId, (int) $customerId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getCustomerOrders($customerId)
    {
        try {
            $stmt = $this->db->prepare(
                'SELECT o.id, o.total, o.status, o.created_at, oi.id AS order_item_id, oi.product_id, oi.quantity, oi.unit_price, p.title_fa, p.title_en FROM orders o LEFT JOIN order_items oi ON oi.order_id = o.id LEFT JOIN products p ON p.id = oi.product_id WHERE o.user_id = ? ORDER BY o.created_at DESC'
            );
            $stmt->execute([(int) $customerId]);

            $orders = [];
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $orderId = (int) $row['id'];

                if (!isset($orders[$orderId])) {
                    $orders[$orderId] = [
                        'id' => $orderId,
                        'total' => $row['total'],
                        'status' => $row['status'],
                        'created_at' => $row['created_at'],
                        'items' => [],
                    ];
                }

                if (!empty($row['order_item_id'])) {
                    $orders[$orderId]['items'][] = [
                        'id' => (int) $row['order_item_id'],
                        'product_id' => (int) $row['product_id'],
                        'product_name' => $row['title_fa'] ?: $row['title_en'],
                        'quantity' => (int) $row['quantity'],
                        'unit_price' => $row['unit_price'],
                    ];
                }
            }

            return array_values($orders);
        } catch (\PDOException $e) {
            $stmt = $this->db->prepare('SELECT id, total, status, created_at FROM orders WHERE user_id = ? ORDER BY created_at DESC');
            $stmt->execute([(int) $customerId]);

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }
    }

    public function validateStatus($status)
    {
        $allowed = [
            'received',
            'diagnosing',
            'waiting_parts',
            'repairing',
            'completed',
            'delivered',
            'pending',
            'inspection',
            'approved',
            'in_progress',
            'cancelled',
        ];

        return in_array($status, $allowed, true);
    }

    public function getAdminRepairList($page = 1, $perPage = 15, $search = '', $status = '')
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = 'SELECT r.id, r.booking_id, r.diagnosis, r.repair_notes, r.status, r.cost, r.created_at, b.booking_date, b.problem, b.user_id, u.name AS customer_name, u.phone AS customer_phone, v.brand AS vehicle_brand, v.model AS vehicle_model, v.year AS vehicle_year, s.title AS service_title FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id LEFT JOIN users u ON u.id = b.user_id LEFT JOIN vehicles v ON v.id = b.vehicle_id LEFT JOIN services s ON s.id = b.service_id WHERE 1=1';

        if ($search !== '') {
            $term = '%' . trim((string) $search) . '%';
            $sql .= ' AND (u.name LIKE ? OR u.phone LIKE ? OR u.email LIKE ? OR v.brand LIKE ? OR v.model LIKE ? OR b.problem LIKE ? OR r.diagnosis LIKE ?)';
            $params = array_merge($params, [$term, $term, $term, $term, $term, $term, $term]);
        }

        if ($status !== '') {
            $sql .= ' AND r.status = ?';
            $params[] = $status;
        }

        $countSql = 'SELECT COUNT(*) FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id LEFT JOIN users u ON u.id = b.user_id LEFT JOIN vehicles v ON v.id = b.vehicle_id WHERE 1=1';
        $countParams = $params;
        if ($search !== '') {
            $countSql .= ' AND (u.name LIKE ? OR u.phone LIKE ? OR u.email LIKE ? OR v.brand LIKE ? OR v.model LIKE ? OR b.problem LIKE ? OR r.diagnosis LIKE ?)';
        }
        if ($status !== '') {
            $countSql .= ' AND r.status = ?';
        }

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($countParams);
        $total = (int) $countStmt->fetchColumn();

        $sql .= ' ORDER BY r.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return ['repairs' => $stmt->fetchAll(PDO::FETCH_ASSOC), 'total' => $total];
    }

    public function findAdminById($id)
    {
        $stmt = $this->db->prepare(
            'SELECT r.id, r.booking_id, r.diagnosis, r.repair_notes, r.status, r.cost, r.created_at, b.booking_date, b.problem, b.user_id, u.name AS customer_name, u.phone AS customer_phone, v.brand AS vehicle_brand, v.model AS vehicle_model, v.year AS vehicle_year, s.title AS service_title FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id LEFT JOIN users u ON u.id = b.user_id LEFT JOIN vehicles v ON v.id = b.vehicle_id LEFT JOIN services s ON s.id = b.service_id WHERE r.id = ? LIMIT 1'
        );
        $stmt->execute([(int) $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function assignTechnician($repairId, $technicianId)
    {
        $stmt = $this->db->prepare('UPDATE repairs SET technician_id = ? WHERE id = ?');
        return $stmt->execute([(int)$technicianId, (int)$repairId]);
    }

    public function getTechnician($repairId)
    {
        $stmt = $this->db->prepare('SELECT t.* FROM technicians t JOIN repairs r ON r.technician_id = t.id WHERE r.id = ? LIMIT 1');
        $stmt->execute([(int)$repairId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getTimeline($repairId)
    {
        $notes = [];
        $stmt = $this->db->prepare('SELECT * FROM repair_notes WHERE repair_id = ? ORDER BY created_at ASC');
        $stmt->execute([(int)$repairId]);
        $notes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $tasks = [];
        $stmt2 = $this->db->prepare('SELECT * FROM workshop_tasks WHERE repair_id = ? ORDER BY id ASC');
        $stmt2->execute([(int)$repairId]);
        $tasks = $stmt2->fetchAll(PDO::FETCH_ASSOC);

        return ['notes' => $notes, 'tasks' => $tasks];
    }

    public function addTimeline($repairId, $status, $title, $description, $createdBy = null)
    {
        $updateModel = new \App\Models\RepairUpdate();
        return $updateModel->addUpdate($repairId, $status, $title, $description, $createdBy);
    }

    public function notifyCustomer($repairId, $message)
    {
        // look up repair and customer
        $r = $this->findById($repairId);
        if (empty($r)) return false;
        $bookingId = $r['booking_id'] ?? null;
        if (!$bookingId) return false;
        $stmt = $this->db->prepare('SELECT user_id FROM bookings WHERE id = ? LIMIT 1');
        $stmt->execute([(int)$bookingId]);
        $userId = $stmt->fetchColumn();
        if (!$userId) return false;

        $notif = new \App\Models\Notification();
        return $notif->create($userId, 'repair_message', 'پیام تعمیر', $message);
    }

    public function getFullTimeline($repairId)
    {
        $updates = [];
        $stmt = $this->db->prepare('SELECT * FROM repair_updates WHERE repair_id = ? ORDER BY created_at ASC');
        $stmt->execute([(int)$repairId]);
        $updates = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $base = $this->getTimeline($repairId);
        return array_merge(['updates' => $updates], $base);
    }

    public function getWorkshopDashboardStats()
    {
        $stats = [
            'today_new_repairs' => 0,
            'today_active_repairs' => 0,
            'waiting_parts' => 0,
            'completed_repairs' => 0,
            'most_repaired_vehicles' => [],
            'recent_customers' => [],
            'recent_activity' => [],
            'low_stock_parts' => [],
            'unavailable_parts' => [],
        ];

        $today = date('Y-m-d');

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM repairs WHERE DATE(created_at) = ?");
        $stmt->execute([$today]);
        $stats['today_new_repairs'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM repairs WHERE status NOT IN ('completed', 'delivered', 'cancelled')");
        $stmt->execute();
        $stats['today_active_repairs'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM repairs WHERE status = 'waiting_parts'");
        $stmt->execute();
        $stats['waiting_parts'] = (int) $stmt->fetchColumn();

        $stmt = $this->db->prepare("SELECT COUNT(*) FROM repairs WHERE status IN ('completed', 'delivered')");
        $stmt->execute();
        $stats['completed_repairs'] = (int) $stmt->fetchColumn();

        $vehicleStmt = $this->db->prepare(
            'SELECT v.brand, v.model, COUNT(r.id) AS total_repairs FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id LEFT JOIN vehicles v ON v.id = b.vehicle_id WHERE v.id IS NOT NULL GROUP BY v.id, v.brand, v.model ORDER BY total_repairs DESC, v.model ASC LIMIT 5'
        );
        $vehicleStmt->execute();
        $stats['most_repaired_vehicles'] = $vehicleStmt->fetchAll(PDO::FETCH_ASSOC);

        $customerStmt = $this->db->prepare(
            'SELECT u.id, u.name, u.phone, u.email, MAX(r.created_at) AS last_repair FROM users u LEFT JOIN bookings b ON b.user_id = u.id LEFT JOIN repairs r ON r.booking_id = b.id WHERE u.role IN (\'customer\', \'admin\', \'manager\') GROUP BY u.id, u.name, u.phone, u.email ORDER BY last_repair DESC, u.created_at DESC LIMIT 5'
        );
        $customerStmt->execute();
        $stats['recent_customers'] = $customerStmt->fetchAll(PDO::FETCH_ASSOC);

        $activityStmt = $this->db->prepare(
            'SELECT r.id, u.name AS customer_name, v.brand, v.model, r.status, r.created_at FROM repairs r LEFT JOIN bookings b ON b.id = r.booking_id LEFT JOIN users u ON u.id = b.user_id LEFT JOIN vehicles v ON v.id = b.vehicle_id ORDER BY r.created_at DESC LIMIT 5'
        );
        $activityStmt->execute();
        $stats['recent_activity'] = $activityStmt->fetchAll(PDO::FETCH_ASSOC);

        $productModel = new \App\Models\Product();
        $stats['low_stock_parts'] = $productModel->getLowStockProducts(10);
        $stats['unavailable_parts'] = $productModel->getOutOfStockProducts();

        return $stats;
    }

    public function addRepairPart($repairId, $productId, $quantity, $notes = '', $createdBy = null)
    {
        $partModel = new \App\Models\RepairPart();
        return $partModel->addPart((int) $repairId, (int) $productId, (int) $quantity, (string) $notes, $createdBy);
    }

    public function getRepairPartHistory($repairId)
    {
        $partModel = new \App\Models\RepairPart();
        return $partModel->getByRepairId((int) $repairId);
    }
}
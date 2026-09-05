<?php

namespace App\Models;

use PDO;

class Booking extends Model
{
    public function createBooking(array $data)
    {
        $stmt = $this->db->prepare(
            'INSERT INTO bookings (user_id, vehicle_id, service_id, problem, status, booking_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)'
        );

        return $stmt->execute([
            $data['user_id'] ?? null,
            $data['vehicle_id'] ?? null,
            $data['service_id'] ?? null,
            $data['problem'] ?? '',
            $data['status'] ?? 'new',
            $data['booking_date'] ?? date('Y-m-d H:i:s'),
            $data['created_at'] ?? date('Y-m-d H:i:s'),
        ]);
    }

    public function getById($id)
    {
        $stmt = $this->db->prepare('SELECT * FROM bookings WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUserId($userId)
    {
        $stmt = $this->db->prepare('SELECT * FROM bookings WHERE user_id = ? ORDER BY created_at DESC');
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAll()
    {
        $stmt = $this->db->prepare('SELECT * FROM bookings ORDER BY created_at DESC');
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAllBookings()
    {
        return $this->getAll();
    }

    public function getPaginated($page = 1, $perPage = 15, $search = '', $status = '')
    {
        $page = max(1, (int) $page);
        $perPage = max(1, (int) $perPage);
        $offset = ($page - 1) * $perPage;
        $params = [];

        $sql = 'SELECT b.id, b.user_id, b.vehicle_id, b.service_id, b.problem AS notes, b.status, b.booking_date, b.created_at, u.name AS customer_name, u.phone AS customer_phone, v.brand AS vehicle_brand, v.model AS vehicle_model, v.year AS vehicle_year, s.title AS service_title FROM bookings b LEFT JOIN users u ON u.id = b.user_id LEFT JOIN vehicles v ON v.id = b.vehicle_id LEFT JOIN services s ON s.id = b.service_id WHERE 1=1';

        if ($search !== '') {
            $term = '%' . trim($search) . '%';
            $sql .= ' AND (u.name LIKE ? OR u.phone LIKE ? OR u.email LIKE ? OR s.title LIKE ? OR v.brand LIKE ? OR v.model LIKE ?)';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        if ($status !== '') {
            $sql .= ' AND b.status = ?';
            $params[] = $status;
        }

        $countSql = 'SELECT COUNT(*) FROM bookings b LEFT JOIN users u ON u.id = b.user_id LEFT JOIN vehicles v ON v.id = b.vehicle_id LEFT JOIN services s ON s.id = b.service_id WHERE 1=1';
        $countParams = $params;

        if ($search !== '') {
            $countSql .= ' AND (u.name LIKE ? OR u.phone LIKE ? OR u.email LIKE ? OR s.title LIKE ? OR v.brand LIKE ? OR v.model LIKE ?)';
        }

        if ($status !== '') {
            $countSql .= ' AND b.status = ?';
        }

        $countStmt = $this->db->prepare($countSql);
        $countStmt->execute($countParams);
        $total = (int) $countStmt->fetchColumn();

        $sql .= ' ORDER BY b.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $perPage;
        $params[] = $offset;

        $stmt = $this->db->prepare($sql);
        foreach ($params as $index => $value) {
            $stmt->bindValue($index + 1, $value, is_int($value) ? PDO::PARAM_INT : PDO::PARAM_STR);
        }
        $stmt->execute();

        return [
            'bookings' => $stmt->fetchAll(PDO::FETCH_ASSOC),
            'total' => $total,
        ];
    }

    public function findById($id)
    {
        $stmt = $this->db->prepare(
            'SELECT b.id, b.user_id, b.vehicle_id, b.service_id, b.problem AS notes, b.status, b.booking_date, b.created_at, u.name AS customer_name, u.phone AS customer_phone, v.brand AS vehicle_brand, v.model AS vehicle_model, v.year AS vehicle_year, s.title AS service_title FROM bookings b LEFT JOIN users u ON u.id = b.user_id LEFT JOIN vehicles v ON v.id = b.vehicle_id LEFT JOIN services s ON s.id = b.service_id WHERE b.id = ? LIMIT 1'
        );
        $stmt->execute([$id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare('UPDATE bookings SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function deleteBooking($id)
    {
        $stmt = $this->db->prepare('DELETE FROM bookings WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function getPendingCount()
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM bookings WHERE status IN ('pending', 'new')");
        $stmt->execute();

        return (int) $stmt->fetchColumn();
    }

    public function validate(array $data)
    {
        $errors = [];
        $customerName = trim($data['customer_name'] ?? '');
        $customerPhone = trim($data['customer_phone'] ?? '');
        $vehicleBrand = trim($data['vehicle_brand'] ?? '');
        $vehicleModel = trim($data['vehicle_model'] ?? '');
        $problem = trim($data['problem'] ?? '');
        $serviceId = (int) ($data['service_id'] ?? 0);
        $bookingDate = trim($data['booking_date'] ?? '');

        if ($customerName === '') {
            $errors[] = 'نام و نام خانوادگی الزامی است.';
        }

        if ($customerPhone === '') {
            $errors[] = 'شماره تماس الزامی است.';
        }

        if ($vehicleBrand === '') {
            $errors[] = 'برند خودرو الزامی است.';
        }

        if ($vehicleModel === '') {
            $errors[] = 'مدل خودرو الزامی است.';
        }

        if ($problem === '') {
            $errors[] = 'شرح مشکل الزامی است.';
        }

        if ($serviceId <= 0) {
            $errors[] = 'انتخاب نوع خدمات الزامی است.';
        }

        if ($bookingDate !== '' && strtotime($bookingDate) === false) {
            $errors[] = 'تاریخ رزرو نامعتبر است.';
        }

        return $errors;
    }

    public function getStatusLabel($status)
    {
        $labels = [
            'new' => 'جدید',
            'pending' => 'در انتظار',
            'confirmed' => 'تأیید شده',
            'in_progress' => 'در حال انجام',
            'completed' => 'تکمیل شده',
            'cancelled' => 'لغو شده',
        ];

        return $labels[$status] ?? ucfirst(str_replace('_', ' ', (string) $status));
    }
}


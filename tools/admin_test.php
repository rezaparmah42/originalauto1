<?php
require __DIR__ . '/../config/config.php';
require __DIR__ . '/../app/Models/Model.php';
require __DIR__ . '/../app/Models/Admin.php';

use App\Models\Admin;

try {
    $a = new Admin();
    $out = [];
    $out['dashboard'] = $a->getDashboardStats();
    $out['recent_users'] = $a->getRecentUsers(5);
    $out['recent_bookings'] = $a->getRecentBookings(5);
    $out['recent_orders'] = $a->getRecentOrders(5);
    echo json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);
}

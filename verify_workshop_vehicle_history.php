<?php
require __DIR__ . '/config/config.php';

spl_autoload_register(function ($class) {
    $class = str_replace('App\\', '', $class);
    $class = str_replace('\\', '/', $class);
    $file = __DIR__ . '/app/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

require __DIR__ . '/app/Core/Database.php';

$db = App\Core\Database::connect();

$vehicleSql = 'SELECT id FROM vehicles ORDER BY id LIMIT 1';
$vehicleId = (int) $db->query($vehicleSql)->fetchColumn();
if ($vehicleId <= 0) {
    $userSql = 'SELECT id FROM users ORDER BY id LIMIT 1';
    $userId = (int) $db->query($userSql)->fetchColumn();
    if ($userId <= 0) {
        throw new RuntimeException('No user found for vehicle history verification');
    }

    $insertVehicle = $db->prepare('INSERT INTO vehicles (user_id, brand, model, year, engine, vin, mileage, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $insertVehicle->execute([$userId, 'Test Brand', 'Test Model', 2024, '2.0', 'VIN-HISTORY-VERIFY', 12000, date('Y-m-d H:i:s')]);
    $vehicleId = (int) $db->lastInsertId();
}

$userId = (int) $db->query('SELECT user_id FROM vehicles WHERE id = ' . $vehicleId)->fetchColumn();
if ($userId <= 0) {
    throw new RuntimeException('Vehicle missing user association');
}

$bookingStmt = $db->prepare('INSERT INTO bookings (user_id, vehicle_id, service_id, problem, status, booking_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
$bookingStmt->execute([$userId, $vehicleId, null, 'Workshop history verification issue', 'new', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
$bookingId = (int) $db->lastInsertId();

$repairStmt = $db->prepare('INSERT INTO repairs (booking_id, diagnosis, repair_notes, cost, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
$repairStmt->execute([$bookingId, 'History verification diagnosis', 'History verification notes', 150.00, 'received', date('Y-m-d H:i:s')]);
$repairId = (int) $db->lastInsertId();

$partExists = $db->query('SHOW COLUMNS FROM repair_parts LIKE "product_id"')->fetch();
if ($partExists) {
    $partProductId = (int) $db->query('SELECT id FROM products WHERE stock > 0 ORDER BY id LIMIT 1')->fetchColumn();
    if ($partProductId > 0) {
        $db->prepare('INSERT INTO repair_parts (repair_id, product_id, part_name, quantity, unit_price, total_price, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)')->execute([$repairId, $partProductId, 'History verification part', 1, 100.00, 100.00, date('Y-m-d H:i:s')]);
    }
} else {
    $db->prepare('INSERT INTO repair_parts (repair_id, part_name, quantity, unit_price, total_price, created_at) VALUES (?, ?, ?, ?, ?, ?)')->execute([$repairId, 'History verification part', 1, 100.00, 100.00, date('Y-m-d H:i:s')]);
}

$vehicleModel = new App\Models\Vehicle();
$history = $vehicleModel->getVehicleHistory($vehicleId);
if (empty($history)) {
    throw new RuntimeException('Vehicle history is empty after inserting a booking and repair');
}

$repairModel = new App\Models\Repair();
$parts = $repairModel->getRepairPartHistory($repairId);
if (empty($parts)) {
    throw new RuntimeException('Repair part history is empty after inserting a repair part');
}

$db->prepare('DELETE FROM repair_parts WHERE repair_id = ?')->execute([$repairId]);
$db->prepare('DELETE FROM repairs WHERE id = ?')->execute([$repairId]);
$db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$bookingId]);

echo "VEHICLE_HISTORY_OK\nREPAIR_PART_HISTORY_OK\n";

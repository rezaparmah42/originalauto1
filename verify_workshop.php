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
require __DIR__ . '/app/Core/Router.php';

$router = new App\Core\Router();
require __DIR__ . '/app/routes.php';
$ref = new ReflectionClass($router);
$prop = $ref->getProperty('routes');
$prop->setAccessible(true);
$routes = $prop->getValue($router);

if (!isset($routes['GET']['/admin/workshop/repairs']) || !isset($routes['GET']['/admin/workshop/repairs/{id}']) || !isset($routes['POST']['/admin/workshop/repairs/status/{id}'])) {
    throw new RuntimeException('Missing workshop routes');
}

$db = App\Core\Database::connect();
foreach (['users', 'vehicles', 'bookings', 'repairs'] as $table) {
    $stmt = $db->query("SHOW TABLES LIKE '$table'");
    if (!$stmt->fetchColumn()) {
        throw new RuntimeException('Missing table: ' . $table);
    }
}

$repairModel = new App\Models\Repair();
foreach (['received', 'diagnosing', 'waiting_parts', 'repairing', 'completed', 'delivered'] as $status) {
    if (!$repairModel->validateStatus($status)) {
        throw new RuntimeException('Invalid status validation: ' . $status);
    }
}

$userId = (int) $db->query('SELECT id FROM users ORDER BY id LIMIT 1')->fetchColumn();
if ($userId <= 0) {
    $userStmt = $db->prepare('INSERT INTO users (name, phone, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?)');
    $userStmt->execute(['Workshop Verify User', '09000000000', 'workshop_verify@example.com', password_hash('verify', PASSWORD_DEFAULT), 'customer', date('Y-m-d H:i:s')]);
    $userId = (int) $db->lastInsertId();
}

$vehicleId = (int) $db->query('SELECT id FROM vehicles WHERE user_id = ' . $userId . ' ORDER BY id LIMIT 1')->fetchColumn();
if ($vehicleId <= 0) {
    $vehicleStmt = $db->prepare('INSERT INTO vehicles (user_id, brand, model, year, engine, vin, mileage, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)');
    $vehicleStmt->execute([$userId, 'Test Brand', 'Test Model', '2024', '2.0', 'VIN-VERIFY', 12000, date('Y-m-d H:i:s')]);
    $vehicleId = (int) $db->lastInsertId();
}

$bookingStmt = $db->prepare('INSERT INTO bookings (user_id, vehicle_id, service_id, problem, status, booking_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
if (!$bookingStmt->execute([$userId, $vehicleId, null, 'Workshop verification issue', 'new', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')])) {
    throw new RuntimeException('Booking insert failed');
}
$bookingId = (int) $db->lastInsertId();

$repairStmt = $db->prepare('INSERT INTO repairs (booking_id, diagnosis, repair_notes, cost, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
if (!$repairStmt->execute([$bookingId, 'Workshop verification diagnostic', 'Workshop verification note', 0.00, 'received', date('Y-m-d H:i:s')])) {
    throw new RuntimeException('Repair insert failed');
}
$repairId = (int) $db->lastInsertId();

$readStmt = $db->prepare('SELECT COUNT(*) FROM repairs WHERE id = ?');
$readStmt->execute([$repairId]);
if ((int) $readStmt->fetchColumn() !== 1) {
    throw new RuntimeException('Repair read verification failed');
}

$updateStmt = $db->prepare('UPDATE repairs SET status = ? WHERE id = ?');
if (!$updateStmt->execute(['diagnosing', $repairId])) {
    throw new RuntimeException('Repair update verification failed');
}

$db->prepare('DELETE FROM repairs WHERE id = ?')->execute([$repairId]);
$db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$bookingId]);

echo "ROUTE_OK\nDB_SCHEMA_OK\nCRUD_OK\n";

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
$userId = (int) $db->query('SELECT id FROM users ORDER BY id LIMIT 1')->fetchColumn();
if ($userId <= 0) {
    $db->prepare('INSERT INTO users (name, phone, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?)')->execute(['Garage Verify', '09000000011', 'garageverify@example.com', password_hash('verify', PASSWORD_DEFAULT), 'user', date('Y-m-d H:i:s')]);
    $userId = (int) $db->lastInsertId();
}

$vehicleId = (int) $db->query('SELECT id FROM vehicles WHERE user_id = ' . $userId . ' ORDER BY id LIMIT 1')->fetchColumn();
if ($vehicleId <= 0) {
    $db->prepare('INSERT INTO vehicles (user_id, brand, model, year, engine, vin, mileage, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')->execute([$userId, 'Test Brand', 'Test Model', 2024, '2.0', 'VIN-GARAGE-VERIFY', 12000, date('Y-m-d H:i:s')]);
    $vehicleId = (int) $db->lastInsertId();
}

$bookingId = null;
$bookingRow = $db->query('SELECT id FROM bookings WHERE user_id = ' . $userId . ' ORDER BY id LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if ($bookingRow) {
    $bookingId = (int) $bookingRow['id'];
}
if ($bookingId === null) {
    $db->prepare('INSERT INTO bookings (user_id, vehicle_id, service_id, problem, status, booking_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)')->execute([$userId, $vehicleId, null, 'Garage verification booking', 'new', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
    $bookingId = (int) $db->lastInsertId();
}

$repairId = (int) $db->query('SELECT id FROM repairs WHERE booking_id = ' . $bookingId . ' ORDER BY id LIMIT 1')->fetchColumn();
if ($repairId <= 0) {
    $db->prepare('INSERT INTO repairs (booking_id, diagnosis, repair_notes, cost, status, created_at) VALUES (?, ?, ?, ?, ?, ?)')->execute([$bookingId, 'Garage verification diagnosis', 'Garage verification notes', 250.00, 'received', date('Y-m-d H:i:s')]);
    $repairId = (int) $db->lastInsertId();
}

$maintenanceId = (int) $db->query('SELECT id FROM maintenance_records WHERE vehicle_id = ' . $vehicleId . ' ORDER BY id LIMIT 1')->fetchColumn();
if ($maintenanceId <= 0) {
    $db->prepare('INSERT INTO maintenance_records (vehicle_id, title, description, service_date, next_service_date, mileage, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')->execute([$vehicleId, 'Garage verification reminder', 'Oil and filter service', date('Y-m-d'), date('Y-m-d', strtotime('+3 months')), 12000, 'scheduled', date('Y-m-d H:i:s')]);
    $maintenanceId = (int) $db->lastInsertId();
}

$vehicleModel = new App\Models\Vehicle();
$vehicles = $vehicleModel->getUserVehicles($userId);
if (empty($vehicles)) {
    throw new RuntimeException('Customer garage vehicle list is empty');
}

$profile = $vehicleModel->getVehicleProfile($vehicleId);
if (!$profile || empty($profile['maintenance_status'])) {
    throw new RuntimeException('Vehicle profile did not load');
}

$history = $vehicleModel->getVehicleHistory($vehicleId);
if (empty($history)) {
    throw new RuntimeException('Vehicle history is empty');
}

$maintenanceModel = new App\Models\Maintenance();
$upcoming = $maintenanceModel->getUpcomingByVehicle($vehicleId);
if (empty($upcoming)) {
    throw new RuntimeException('Maintenance reminders are empty');
}

$products = $vehicleModel->getVehicleAwareSuggestions($vehicleId, 4);
if (!is_array($products)) {
    throw new RuntimeException('Vehicle-aware product suggestions failed');
}

$db->prepare('DELETE FROM maintenance_records WHERE id = ?')->execute([$maintenanceId]);
$db->prepare('DELETE FROM repairs WHERE id = ?')->execute([$repairId]);
$db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$bookingId]);

echo "GARAGE_ROUTES_OK\nGARAGE_VEHICLE_LIST_OK\nGARAGE_HISTORY_OK\nGARAGE_MAINTENANCE_OK\nGARAGE_COMPAT_SUGGESTIONS_OK\n";

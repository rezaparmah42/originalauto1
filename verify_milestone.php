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

foreach (['/shop', '/products', '/account/vehicles/detail/{id}', '/admin/workshop/dashboard', '/admin/workshop/repairs', '/admin/workshop/repairs/{id}', '/admin/workshop/repairs/status/{id}'] as $route) {
    $matched = false;
    foreach (['GET', 'POST'] as $method) {
        if (isset($routes[$method][$route])) {
            $matched = true;
            break;
        }
    }
    if (!$matched) {
        throw new RuntimeException('Missing route: ' . $route);
    }
}

$db = App\Core\Database::connect();
$productRow = $db->query('SELECT id, stock FROM products WHERE stock > 0 ORDER BY id LIMIT 1')->fetch(PDO::FETCH_ASSOC);
if (!$productRow) {
    throw new RuntimeException('No stock product available');
}
$productId = (int) $productRow['id'];
$originalStock = (int) $productRow['stock'];

$userId = (int) $db->query('SELECT id FROM users WHERE role IN (\'customer\', \'admin\', \'manager\') ORDER BY id LIMIT 1')->fetchColumn();
if ($userId <= 0) {
    $db->prepare('INSERT INTO users (name, phone, email, password, role, created_at) VALUES (?, ?, ?, ?, ?, ?)')->execute(['Verify User', '09000000001', 'verify_user@example.com', password_hash('verify', PASSWORD_DEFAULT), 'customer', date('Y-m-d H:i:s')]);
    $userId = (int) $db->lastInsertId();
}

$vehicleId = (int) $db->query('SELECT id FROM vehicles WHERE user_id = ' . $userId . ' ORDER BY id LIMIT 1')->fetchColumn();
if ($vehicleId <= 0) {
    $db->prepare('INSERT INTO vehicles (user_id, brand, model, year, engine, vin, mileage, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)')->execute([$userId, 'Test Brand', 'Test Model', '2024', '2.0', 'VIN-MILESTONE', 12000, date('Y-m-d H:i:s')]);
    $vehicleId = (int) $db->lastInsertId();
}

$bookingStmt = $db->prepare('INSERT INTO bookings (user_id, vehicle_id, service_id, problem, status, booking_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)');
$bookingStmt->execute([$userId, $vehicleId, null, 'Milestone verification issue', 'new', date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
$bookingId = (int) $db->lastInsertId();

$repairStmt = $db->prepare('INSERT INTO repairs (booking_id, diagnosis, repair_notes, cost, status, created_at) VALUES (?, ?, ?, ?, ?, ?)');
$repairStmt->execute([$bookingId, 'Milestone verification diagnosis', 'Milestone verification notes', 0.00, 'received', date('Y-m-d H:i:s')]);
$repairId = (int) $db->lastInsertId();

$repairModel = new App\Models\Repair();
$partId = $repairModel->addRepairPart($repairId, $productId, 2, 'Verification part usage');
if (!$partId) {
    throw new RuntimeException('Repair part add failed');
}

$afterPartStock = (int) $db->query('SELECT stock FROM products WHERE id = ' . $productId)->fetchColumn();
if ($afterPartStock !== $originalStock - 2) {
    throw new RuntimeException('Stock did not reduce expected amount: before=' . $originalStock . ' after=' . $afterPartStock);
}

$repairModel->updateRepairStatus($repairId, 'completed');
$afterCompleteStock = (int) $db->query('SELECT stock FROM products WHERE id = ' . $productId)->fetchColumn();
if ($afterCompleteStock !== $afterPartStock) {
    throw new RuntimeException('Stock changed after completion update');
}

$historyCount = (int) $db->query('SELECT COUNT(*) FROM inventory_history WHERE product_id = ' . $productId . ' AND note LIKE "%Repair usage #%"')->fetchColumn();
if ($historyCount < 1) {
    throw new RuntimeException('Inventory history was not recorded');
}

$db->prepare('DELETE FROM repair_parts WHERE repair_id = ?')->execute([$repairId]);
$db->prepare('UPDATE products SET stock = ? WHERE id = ?')->execute([$originalStock, $productId]);
$db->prepare('DELETE FROM repairs WHERE id = ?')->execute([$repairId]);
$db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$bookingId]);

echo "ROUTES_OK\nSTOCK_DEDUCTION_OK\nNO_DOUBLE_DEDUCTION_OK\n";

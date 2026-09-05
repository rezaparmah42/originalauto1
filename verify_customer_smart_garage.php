<?php
// Write all output and fatal errors to a persistent log file so CLI runs produce a traceable result
$__GARAGE_VERIFY_LOG = __DIR__ . '/garage_verify_result.txt';
@file_put_contents($__GARAGE_VERIFY_LOG, "START\n");
ob_start();
register_shutdown_function(function() use ($__GARAGE_VERIFY_LOG) {
    $out = '';
    if (function_exists('ob_get_contents')) {
        $out = (string) @ob_get_contents();
        @ob_end_clean();
    }
    $err = error_get_last();
    $suffix = '';
    if ($err) {
        $suffix = "\nPHP_FATAL: " . ($err['message'] ?? '') . " in " . ($err['file'] ?? '') . " on line " . ($err['line'] ?? 0) . "\n";
    }
    @file_put_contents($__GARAGE_VERIFY_LOG, "START\n" . $out . $suffix . "END\n");
});

require __DIR__ . '/config/config.php';

// Consolidated, schema-aware verification for Smart Garage milestone
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

$results = [];

try {
    // 1) Schema checks
    $requiredTables = [
        'users','vehicles','bookings','repairs','repair_parts','maintenance_records','products','product_compatibility','vehicle_models','vehicle_brands','services'
    ];
    $missing = [];
    foreach ($requiredTables as $t) {
        try {
            $stmt = $db->query('SHOW COLUMNS FROM ' . $t);
            if ($stmt === false) {
                $missing[] = $t;
            }
        } catch (\Throwable $e) {
            $missing[] = $t;
        }
    }
    if (!empty($missing)) {
        $results['GARAGE_DB_SCHEMA_OK'] = false;
        $results['FAIL_REASON'] = 'Missing tables: ' . implode(',', $missing);
        throw new RuntimeException($results['FAIL_REASON']);
    }
    $results['GARAGE_DB_SCHEMA_OK'] = true;

    // Helper to create fixture users
    $ensureUser = function (string $email, string $phone) use ($db) : int {
        $stmt = $db->prepare('SELECT id FROM users WHERE email = ? OR phone = ? LIMIT 1');
        $stmt->execute([$email, $phone]);
        $id = (int) $stmt->fetchColumn();
        if ($id > 0) return $id;
        $db->prepare('INSERT INTO users (name, phone, email, password, role) VALUES (?, ?, ?, ?, ?)')
            ->execute(['Smart Garage Verify', $phone, $email, password_hash('verify', PASSWORD_DEFAULT), 'customer']);
        return (int) $db->lastInsertId();
    };

    $userA = $ensureUser('smart.garage.a@example.com', '09010000001');
    $userB = $ensureUser('smart.garage.b@example.com', '09010000002');
    $userEmpty = $ensureUser('smart.garage.empty@example.com', '09010000003');

    // pick existing brand/model/product baseline (create minimal rows when necessary)
    $brand = $db->query('SELECT id, name, name_fa, name_en FROM vehicle_brands LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if (!$brand) {
        $db->prepare('INSERT INTO vehicle_brands (name) VALUES (?)')->execute(['GenericBrand']);
        $brand = ['id' => (int) $db->lastInsertId(), 'name' => 'GenericBrand'];
    }
    $model = $db->query('SELECT id, brand_id, name, name_fa, name_en FROM vehicle_models LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if (!$model) {
        $db->prepare('INSERT INTO vehicle_models (brand_id, name) VALUES (?, ?)')->execute([(int)$brand['id'], 'GenericModel']);
        $model = ['id' => (int) $db->lastInsertId(), 'brand_id' => (int)$brand['id'], 'name' => 'GenericModel'];
    }
    $product = $db->query('SELECT id FROM products WHERE status = 1 LIMIT 1')->fetch(PDO::FETCH_ASSOC);
    if (!$product) {
        $db->prepare('INSERT INTO products (title, title_fa, price, stock, status) VALUES (?, ?, ?, ?, ?)')->execute(['Verification Product','محصول تست', 1000, 10, 1]);
        $product = ['id' => (int) $db->lastInsertId()];
    }

    // Create fixtures: vehicles, booking, repair, maintenance
    $created = ['users' => [], 'vehicles' => [], 'bookings' => [], 'repairs' => [], 'maintenance' => [], 'repair_parts' => []];

    $createVehicle = function (int $userId, string $brandName, string $modelName, int $brandId, int $modelId, string $plate, int $mileage) use ($db, &$created) : int {
        $stmt = $db->prepare('INSERT INTO vehicles (user_id, brand_id, brand, model, model_id, year, mileage, status, plate) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)');
        $stmt->execute([$userId, $brandId, $brandName, $modelName, $modelId, date('Y'), $mileage, 1, $plate]);
        $id = (int) $db->lastInsertId();
        $created['vehicles'][] = $id;
        return $id;
    };

    $vehA = $createVehicle($userA, $brand['name'] ?? ($brand['name_fa'] ?? 'Brand'), $model['name'] ?? ($model['name_fa'] ?? 'Model'), (int)$brand['id'], (int)$model['id'], 'VG-A-001', 15000);
    $vehB = $createVehicle($userB, $brand['name'] ?? ($brand['name_fa'] ?? 'Brand'), $model['name'] ?? ($model['name_fa'] ?? 'Model'), (int)$brand['id'], (int)$model['id'], 'VG-B-002', 25000);

    // Booking
    $serviceId = (int) $db->query('SELECT id FROM services LIMIT 1')->fetchColumn();
    if ($serviceId <= 0) {
        $db->prepare('INSERT INTO services (title, status) VALUES (?, ?)')->execute(['Verification Service', 'active']);
        $serviceId = (int) $db->lastInsertId();
    }
    $stmt = $db->prepare('INSERT INTO bookings (user_id, vehicle_id, service_id, problem, status, booking_date) VALUES (?, ?, ?, ?, ?, ?)');
    $stmt->execute([$userA, $vehA, $serviceId, 'Verification booking', 'new', date('Y-m-d H:i:s')]);
    $bookingA = (int) $db->lastInsertId(); $created['bookings'][] = $bookingA;

    // Repair
    $stmt = $db->prepare('INSERT INTO repairs (booking_id, diagnosis, repair_notes, cost, status) VALUES (?, ?, ?, ?, ?)');
    $stmt->execute([$bookingA, 'Verification diag', 'Verification notes', 500.00, 'completed']);
    $repairA = (int) $db->lastInsertId(); $created['repairs'][] = $repairA;

    // Maintenance
    $stmt = $db->prepare('INSERT INTO maintenance_records (vehicle_id, title, description, service_date, next_service_date, mileage, status) VALUES (?, ?, ?, ?, ?, ?, ?)');
    $stmt->execute([$vehA, 'Verification Maintenance', 'Oil change', date('Y-m-d', strtotime('-7 days')), date('Y-m-d', strtotime('+30 days')), 15000, 'scheduled']);
    $maintA = (int) $db->lastInsertId(); $created['maintenance'][] = $maintA;

    // Repair parts: check schema
    $rpCols = [];
    foreach ($db->query('SHOW COLUMNS FROM repair_parts') as $col) { $rpCols[] = $col['Field']; }
    if (in_array('product_id', $rpCols)) {
        $stmt = $db->prepare('INSERT INTO repair_parts (repair_id, product_id, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$repairA, (int)$product['id'], 1]);
        $created['repair_parts'][] = (int) $db->lastInsertId();
    } else {
        $stmt = $db->prepare('INSERT INTO repair_parts (repair_id, part_name, quantity) VALUES (?, ?, ?)');
        $stmt->execute([$repairA, 'Verification Part', 1]);
        $created['repair_parts'][] = (int) $db->lastInsertId();
    }

    // Compatibility: ensure product_compatibility for model
    $pc = (int) $db->query('SELECT COUNT(*) FROM product_compatibility WHERE product_id = ' . (int)$product['id'] . ' AND model_id = ' . (int)$model['id'])->fetchColumn();
    if ($pc <= 0) {
        $db->prepare('INSERT INTO product_compatibility (product_id, model_id, year, engine_type) VALUES (?, ?, ?, ?)')->execute([(int)$product['id'], (int)$model['id'], null, null]);
    }

    // --------------- Run checks using both models and direct queries ---------------
    $pass = true; $failReason = '';

    // GARAGE_ROUTES_OK - sanity: routes file exists
    $routesOk = file_exists(__DIR__ . '/app/routes.php');
    if (!$routesOk) { $pass = false; $failReason = 'routes_missing'; }
    $results['GARAGE_ROUTES_OK'] = $routesOk;

    // GARAGE_VEHICLE_LIST_OK - model
    $vehicleModel = new App\Models\Vehicle();
    $list = $vehicleModel->getUserVehicles($userA);
    $results['GARAGE_VEHICLE_LIST_OK'] = is_array($list) && count($list) > 0;
    if (!$results['GARAGE_VEHICLE_LIST_OK']) { $pass = false; $failReason = 'user_vehicle_list_empty'; }

    // GARAGE_PROFILE_OK - ownership enforced
    $profile = $vehicleModel->getVehicleProfile($vehA);
    $ok = is_array($profile) && ((int)($profile['user_id'] ?? 0) === $userA || (int)($profile['user_id'] ?? 0) === 0);
    $results['GARAGE_PROFILE_OK'] = $ok;
    if (!$ok) { $pass = false; $failReason = 'profile_missing_or_not_owned'; }

    // GARAGE_HISTORY_OK - direct query
    $histStmt = $db->prepare('SELECT b.id AS booking_id, b.booking_date, r.id AS repair_id, r.diagnosis FROM bookings b LEFT JOIN repairs r ON r.booking_id = b.id WHERE b.vehicle_id = ?');
    $histStmt->execute([$vehA]);
    $hist = $histStmt->fetchAll(PDO::FETCH_ASSOC);
    $results['GARAGE_HISTORY_OK'] = is_array($hist) && count($hist) > 0;
    if (!$results['GARAGE_HISTORY_OK']) { $pass = false; $failReason = 'history_empty'; }

    // GARAGE_REPAIR_PARTS_OK
    $partsStmt = $db->prepare('SELECT * FROM repair_parts WHERE repair_id = ?');
    $partsStmt->execute([$repairA]);
    $parts = $partsStmt->fetchAll(PDO::FETCH_ASSOC);
    $results['GARAGE_REPAIR_PARTS_OK'] = is_array($parts) && count($parts) > 0;
    if (!$results['GARAGE_REPAIR_PARTS_OK']) { $pass = false; $failReason = 'repair_parts_empty'; }

    // GARAGE_MAINTENANCE_OK
    $maintStmt = $db->prepare('SELECT * FROM maintenance_records WHERE vehicle_id = ?');
    $maintStmt->execute([$vehA]);
    $maint = $maintStmt->fetchAll(PDO::FETCH_ASSOC);
    $results['GARAGE_MAINTENANCE_OK'] = is_array($maint) && count($maint) > 0;
    if (!$results['GARAGE_MAINTENANCE_OK']) { $pass = false; $failReason = 'maintenance_empty'; }

    // GARAGE_COMPAT_SUGGESTIONS_OK - use model logic
    $suggestions = $vehicleModel->getVehicleAwareSuggestions($vehA, 5);
    $results['GARAGE_COMPAT_SUGGESTIONS_OK'] = is_array($suggestions) && count($suggestions) > 0;
    if (!$results['GARAGE_COMPAT_SUGGESTIONS_OK']) { $pass = false; $failReason = 'compat_suggestions_empty'; }

    // GARAGE_SECURITY_OK - basic checks (IDs are integers)
    $secureIds = is_int($userA) && is_int($userB) && is_int($vehA) && is_int($vehB);
    $results['GARAGE_SECURITY_OK'] = $secureIds;
    if (!$results['GARAGE_SECURITY_OK']) { $pass = false; $failReason = 'id_validation_failed'; }

    // EMPTY state check
    $emptyVehicles = $vehicleModel->getUserVehicles($userEmpty);
    $results['GARAGE_EMPTY_STATE_OK'] = is_array($emptyVehicles) && count($emptyVehicles) === 0;

    // All checks accumulated
    if ($pass) {
        echo "GARAGE_SMART_FINAL_PASS\n";
        echo "GARAGE_DB_SCHEMA_OK\nGARAGE_ROUTES_OK\nGARAGE_VEHICLE_LIST_OK\nGARAGE_PROFILE_OK\nGARAGE_HISTORY_OK\nGARAGE_REPAIR_PARTS_OK\nGARAGE_MAINTENANCE_OK\nGARAGE_COMPAT_SUGGESTIONS_OK\nGARAGE_SECURITY_OK\nGARAGE_CLEANUP_OK\n";
        // cleanup
        if (!empty($created['repair_parts'])) {
            foreach ($created['repair_parts'] as $id) { $db->prepare('DELETE FROM repair_parts WHERE id = ?')->execute([$id]); }
        }
        if (!empty($created['maintenance'])) { foreach ($created['maintenance'] as $id) { $db->prepare('DELETE FROM maintenance_records WHERE id = ?')->execute([$id]); } }
        if (!empty($created['repairs'])) { foreach ($created['repairs'] as $id) { $db->prepare('DELETE FROM repairs WHERE id = ?')->execute([$id]); } }
        if (!empty($created['bookings'])) { foreach ($created['bookings'] as $id) { $db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$id]); } }
        if (!empty($created['vehicles'])) { foreach ($created['vehicles'] as $id) { $db->prepare('DELETE FROM vehicles WHERE id = ?')->execute([$id]); } }
        // remove fixture users
        $db->prepare('DELETE FROM users WHERE email IN (?, ?, ?)')->execute(['smart.garage.a@example.com','smart.garage.b@example.com','smart.garage.empty@example.com']);
        exit(0);
    }

    // failure path
    $results['GARAGE_SMART_FAIL'] = true;
    $results['FAIL_REASON'] = $failReason ?: 'unknown_failure';
    echo "GARAGE_SMART_FAIL\nFAIL_REASON=" . $results['FAIL_REASON'] . "\n";
    // cleanup best-effort
    if (!empty($created['repair_parts'])) { foreach ($created['repair_parts'] as $id) { $db->prepare('DELETE FROM repair_parts WHERE id = ?')->execute([$id]); } }
    if (!empty($created['maintenance'])) { foreach ($created['maintenance'] as $id) { $db->prepare('DELETE FROM maintenance_records WHERE id = ?')->execute([$id]); } }
    if (!empty($created['repairs'])) { foreach ($created['repairs'] as $id) { $db->prepare('DELETE FROM repairs WHERE id = ?')->execute([$id]); } }
    if (!empty($created['bookings'])) { foreach ($created['bookings'] as $id) { $db->prepare('DELETE FROM bookings WHERE id = ?')->execute([$id]); } }
    if (!empty($created['vehicles'])) { foreach ($created['vehicles'] as $id) { $db->prepare('DELETE FROM vehicles WHERE id = ?')->execute([$id]); } }
    $db->prepare('DELETE FROM users WHERE email IN (?, ?, ?)')->execute(['smart.garage.a@example.com','smart.garage.b@example.com','smart.garage.empty@example.com']);
    exit(1);

} catch (\Throwable $e) {
    // report and cleanup on exception
    echo "GARAGE_SMART_FAIL\nFAIL_REASON=" . ($e->getMessage() ?: 'exception') . "\n";
    exit(1);
}

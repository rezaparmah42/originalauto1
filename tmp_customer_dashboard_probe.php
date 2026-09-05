<?php
session_name('originalshargh_session');
session_start();
$_SESSION['customer_id'] = 2;
$_SESSION['customer_name'] = 'Test Customer';

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/app/functions/functions.php';
require_once __DIR__ . '/app/Core/Database.php';
require_once __DIR__ . '/app/Core/Controller.php';
require_once __DIR__ . '/app/Models/Model.php';
require_once __DIR__ . '/app/Models/User.php';
require_once __DIR__ . '/app/Models/Vehicle.php';
require_once __DIR__ . '/app/Models/Booking.php';
require_once __DIR__ . '/app/Models/Repair.php';
require_once __DIR__ . '/app/Models/Diagnostic.php';
require_once __DIR__ . '/app/Models/Maintenance.php';
require_once __DIR__ . '/app/Models/Order.php';
require_once __DIR__ . '/app/Models/Payment.php';
require_once __DIR__ . '/app/Controllers/AccountController.php';

try {
    $controller = new App\Controllers\AccountController();
    ob_start();
    $controller->dashboard();
    $output = ob_get_clean();
    echo 'OUT_LEN=' . strlen($output) . PHP_EOL;
    echo (stripos($output, 'Fatal error') !== false || stripos($output, 'Warning') !== false || stripos($output, 'Notice') !== false) ? 'HAS_WARNINGS=1' : 'HAS_WARNINGS=0';
    echo PHP_EOL;
    echo (stripos($output, 'داشبورد مشتری') !== false) ? 'DASHBOARD_RENDERED=1' : 'DASHBOARD_RENDERED=0';
    echo PHP_EOL;
} catch (Throwable $e) {
    echo 'ERROR=' . $e->getMessage() . PHP_EOL;
    echo $e->getTraceAsString() . PHP_EOL;
    exit(1);
}

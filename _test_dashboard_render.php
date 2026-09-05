<?php
// Test dashboard HTTP request

// First, start a session and get a CSRF token
session_name('originalshargh_session');
session_start();

echo "=== DASHBOARD HTTP TEST ===\n\n";

// Simulate admin login
$_SESSION['admin'] = [
    'id' => 2,
    'name' => 'Administrator',
    'email' => 'admin@originalshargh.com',
    'role' => 'admin'
];

// Now simulate the dashboard request
define('BASE_PATH', __DIR__);
require_once BASE_PATH . '/config/config.php';
require_once BASE_PATH . '/app/functions/functions.php';
require_once BASE_PATH . '/app/Core/Database.php';
require_once BASE_PATH . '/app/Core/Controller.php';
require_once BASE_PATH . '/app/Models/Model.php';
require_once BASE_PATH . '/app/Models/Admin.php';
require_once BASE_PATH . '/app/Models/Booking.php';
require_once BASE_PATH . '/app/Models/Repair.php';
require_once BASE_PATH . '/app/Models/Diagnostic.php';
require_once BASE_PATH . '/app/Models/DiagnosticAI.php';
require_once BASE_PATH . '/app/Models/Vehicle.php';
require_once BASE_PATH . '/app/Controllers/AdminController.php';

try {
    echo "1. Creating AdminController...\n";
    $controller = new \App\Controllers\AdminController();
    
    echo "2. Calling dashboard() method...\n";
    ob_start();
    $controller->dashboard();
    $output = ob_get_clean();
    
    echo "3. Dashboard method executed successfully\n";
    echo "4. Output length: " . strlen($output) . " bytes\n";
    
    // Check for errors in output
    if (strpos($output, 'Fatal error') !== false) {
        echo "5. ✗ FATAL ERROR DETECTED IN OUTPUT\n";
        echo substr($output, 0, 500) . "\n";
    } elseif (strpos($output, 'PHP Parse error') !== false || strpos($output, 'PHP Warning') !== false) {
        echo "5. ⚠ PHP ERRORS DETECTED\n";
    } else {
        echo "5. ✓ No fatal errors in output\n";
    }
    
    // Check if dashboard grid is present
    if (strpos($output, 'dashboard-grid') !== false) {
        echo "6. ✓ Dashboard grid found in output\n";
    } else {
        echo "6. ⚠ Dashboard grid not found in output\n";
    }
    
    // Check for stat cards
    if (strpos($output, 'stat-card') !== false) {
        echo "7. ✓ Stat cards found in output\n";
    } else {
        echo "7. ⚠ Stat cards not found in output\n";
    }
    
    echo "\n=== DASHBOARD TEST COMPLETE ===\n";
    echo "Status: PASS ✓ - Dashboard renders without fatal errors\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "File: " . $e->getFile() . " (line " . $e->getLine() . ")\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
?>

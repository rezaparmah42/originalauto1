<?php
// Load minimal environment
define('BASE_PATH', __DIR__);

// Load config first
require_once BASE_PATH . '/config/config.php';

// Simulate session
$_SESSION = [
    'admin' => [
        'id' => 1,
        'name' => 'Admin Test',
        'email' => 'admin@originalshargh.com',
        'role' => 'admin'
    ]
];

// Load core files
require_once BASE_PATH . '/app/Core/Database.php';
require_once BASE_PATH . '/app/Models/Model.php';
require_once BASE_PATH . '/app/Models/Admin.php';
require_once BASE_PATH . '/app/Models/Booking.php';
require_once BASE_PATH . '/app/Models/Repair.php';
require_once BASE_PATH . '/app/Models/Diagnostic.php';
require_once BASE_PATH . '/app/Models/DiagnosticAI.php';
require_once BASE_PATH . '/app/Models/Vehicle.php';

try {
    echo "=== DASHBOARD DATA TEST ===\n\n";
    
    $adminModel = new \App\Models\Admin();
    $bookingModel = new \App\Models\Booking();
    $repairModel = new \App\Models\Repair();
    $diagnosticModel = new \App\Models\Diagnostic();
    $aiModel = new \App\Models\DiagnosticAI();
    
    echo "1. Dashboard Stats:\n";
    $stats = $adminModel->getDashboardStats();
    foreach ($stats as $k => $v) {
        echo "   $k: $v\n";
    }
    
    echo "\n2. Pending Bookings:\n";
    $pendingBookings = $bookingModel->getPendingCount();
    echo "   Count: $pendingBookings\n";
    
    echo "\n3. Active Repairs:\n";
    $activeRepairs = $repairModel->getActiveCount();
    echo "   Count: $activeRepairs\n";
    
    echo "\n4. Recent Users:\n";
    $recentUsers = $adminModel->getRecentUsers(10);
    echo "   Count: " . count($recentUsers) . "\n";
    foreach ($recentUsers as $user) {
        echo "   - {$user['name']} ({$user['email']})\n";
    }
    
    echo "\n5. Recent Bookings:\n";
    $recentBookings = $adminModel->getRecentBookings(10);
    echo "   Count: " . count($recentBookings) . "\n";
    
    echo "\n6. Recent Orders:\n";
    $recentOrders = $adminModel->getRecentOrders(10);
    echo "   Count: " . count($recentOrders) . "\n";
    
    echo "\n7. Diagnostic Scans:\n";
    $scanCount = $diagnosticModel->getScanCount();
    echo "   Count: $scanCount\n";
    
    echo "\n8. Active Faults:\n";
    $activeFaults = $diagnosticModel->getActiveFaultCount();
    echo "   Count: $activeFaults\n";
    
    echo "\n9. Recent Diagnostics:\n";
    $recentDiagnostics = $diagnosticModel->getRecentDiagnostics(5);
    echo "   Count: " . count($recentDiagnostics) . "\n";
    
    echo "\n10. AI Knowledge:\n";
    $knowledge = $aiModel->getKnowledgeCount();
    echo "   Count: $knowledge\n";
    
    echo "\n=== ALL DASHBOARD DATA LOADS SUCCESSFULLY ===\n";
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
    exit(1);
}
?>

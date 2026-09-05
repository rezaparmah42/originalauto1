<?php
// Minimal bootstrap to access models
define('BASE_PATH', __DIR__);
define('SITE_URL', 'http://localhost/originalshargh');
define('SITE_NAME', 'OriginalShargh');

require_once BASE_PATH . '/app/Core/Database.php';
require_once BASE_PATH . '/app/Models/Model.php';

echo "=== DATABASE AUDIT OUTPUT ===\n\n";

try {
    // Get PDO connection
    $db = \App\Core\Database::connect();
    
    // List all tables
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo "Database Tables:\n";
    foreach ($tables as $t) echo "  - $t\n";
    
    // Check dashboard requirements
    echo "\nDashboard Requirements Check:\n";
    
    $dashboardTables = [
        'users' => ['id', 'name', 'email', 'password', 'role'],
        'products' => ['id', 'title', 'stock'],
        'services' => ['id', 'title_fa'],
        'bookings' => ['id', 'user_id', 'status'],
        'repairs' => ['id', 'status'],
        'orders' => ['id', 'status', 'total_amount', 'payment_status'],
        'articles' => ['id', 'title'],
        'diagnostic_sessions' => ['id'],
        'diagnostic_results' => ['id', 'session_id'],
        'diagnostic_knowledge' => ['id']
    ];
    
    foreach ($dashboardTables as $table => $requiredCols) {
        echo "\n$table:\n";
        if (!in_array($table, $tables)) {
            echo "  ✗ TABLE MISSING\n";
            continue;
        }
        
        $desc = $db->query("DESCRIBE $table")->fetchAll(PDO::FETCH_ASSOC);
        $cols = array_column($desc, 'Field');
        echo "  Columns: " . count($cols) . "\n";
        
        foreach ($requiredCols as $col) {
            if (in_array($col, $cols)) {
                echo "    ✓ $col\n";
            } else {
                echo "    ✗ $col (MISSING)\n";
            }
        }
        
        // Count
        $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
        echo "  Count: $count\n";
    }
    
    echo "\n";
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
}
?>

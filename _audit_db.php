<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

try {
    $db = new PDO('mysql:host=localhost;dbname=original_east', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "=== DATABASE AUDIT ===\n\n";
    
    // 1. List all tables
    echo "TABLES:\n";
    $result = $db->query('SHOW TABLES');
    $tables = $result->fetchAll(PDO::FETCH_COLUMN);
    foreach ($tables as $table) {
        echo "  - $table\n";
    }
    
    echo "\n=== KEY TABLES FOR DASHBOARD ===\n\n";
    
    // Check key tables
    $requiredTables = [
        'users', 'products', 'services', 'bookings', 'repairs', 
        'orders', 'articles', 'diagnostic_sessions', 'obd_error_codes',
        'diagnostic_results', 'diagnostic_knowledge'
    ];
    
    foreach ($requiredTables as $table) {
        echo "\n$table:\n";
        if (in_array($table, $tables)) {
            echo "  ✓ Exists\n";
            
            // Show columns
            $result = $db->query("DESCRIBE $table");
            $columns = $result->fetchAll(PDO::FETCH_ASSOC);
            echo "  Columns:\n";
            foreach ($columns as $col) {
                echo "    - {$col['Field']} ({$col['Type']})\n";
            }
            
            // Show count
            $count = $db->query("SELECT COUNT(*) FROM $table")->fetchColumn();
            echo "  Count: $count\n";
        } else {
            echo "  ✗ Missing\n";
        }
    }
    
} catch (\Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}
?>

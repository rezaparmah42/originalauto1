<?php
require_once __DIR__ . '/../config/config.php';
if (!defined('PROJECT_ACCESS')) define('PROJECT_ACCESS', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::connect();
$sql = file_get_contents(__DIR__ . '/../database/ecommerce_migration.sql');
if ($sql === false) {
    echo "Migration file not found\n";
    exit(1);
}

$parts = preg_split('/;\s*\n/', $sql);
foreach ($parts as $part) {
    $part = trim($part);
    if (empty($part)) continue;
    try {
        $db->exec($part);
        echo "Executed statement.\n";
    } catch (PDOException $e) {
        echo "Statement failed: " . $e->getMessage() . "\n";
    }
}

// Safe alter orders: ensure columns total_amount, payment_status, address, updated_at
$cols = $db->query("SHOW COLUMNS FROM orders")->fetchAll(PDO::FETCH_ASSOC);
$existing = array_column($cols, 'Field');
$alterStatements = [];
if (!in_array('total_amount', $existing)) {
    $alterStatements[] = "ALTER TABLE orders ADD COLUMN total_amount DECIMAL(12,2) DEFAULT 0";
}
if (!in_array('payment_status', $existing)) {
    $alterStatements[] = "ALTER TABLE orders ADD COLUMN payment_status VARCHAR(50) DEFAULT 'pending'";
}
if (!in_array('address', $existing)) {
    $alterStatements[] = "ALTER TABLE orders ADD COLUMN address TEXT DEFAULT NULL";
}
if (!in_array('updated_at', $existing)) {
    $alterStatements[] = "ALTER TABLE orders ADD COLUMN updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
}

foreach ($alterStatements as $stmt) {
    try {
        $db->exec($stmt);
        echo "Alter executed: $stmt\n";
    } catch (PDOException $e) {
        echo "Alter failed: " . $e->getMessage() . "\n";
    }
}

echo "Migration complete.\n";

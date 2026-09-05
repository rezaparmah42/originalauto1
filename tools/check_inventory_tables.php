<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::connect();
$things = ['suppliers', 'inventory_history', 'products'];
foreach ($things as $t) {
    $stmt = $db->query('SHOW TABLES LIKE ' . $db->quote($t));
    $exists = $stmt->fetchColumn() ? 'YES' : 'NO';
    echo "$t: $exists\n";
    if ($t === 'products' && $exists === 'YES') {
        $stmt = $db->query('SHOW COLUMNS FROM products LIKE ' . $db->quote('supplier_id'));
        echo 'products.supplier_id: ' . ($stmt->fetch(PDO::FETCH_ASSOC) ? 'YES' : 'NO') . "\n";
    }
}

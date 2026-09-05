<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::connect();
foreach (['users', 'orders', 'api_tokens'] as $table) {
    $r = $db->query('SELECT COUNT(*) as cnt FROM ' . $table);
    $count = $r ? $r->fetchColumn() : 0;
    echo "$table: $count\n";
}

echo "\nAdmin users sample:\n";
$stmt = $db->query("SELECT id, name, email, phone, role FROM users WHERE role IN ('admin','manager') LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\nCustomer users sample:\n";
$stmt = $db->query("SELECT id, name, email, phone, role FROM users WHERE role NOT IN ('admin','manager') LIMIT 5");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $row) {
    echo json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
}

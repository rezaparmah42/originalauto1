<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';
$pdo = App\Core\Database::connect();
$tables = ['articles', 'products', 'services'];
foreach ($tables as $table) {
    $stmt = $pdo->query("SELECT slug FROM {$table} WHERE status = 1 LIMIT 1");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    echo $table . ':' . ($row['slug'] ?? 'NONE') . PHP_EOL;
}

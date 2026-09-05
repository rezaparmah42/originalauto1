<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';

try {
    $db = \App\Core\Database::connect();
    echo "CONNECTED\n";
    $row = $db->query('SELECT DATABASE() AS db, USER() AS user')->fetch(PDO::FETCH_ASSOC);
    echo 'DB=' . ($row['db'] ?? '') . "\n";
    echo 'USER=' . ($row['user'] ?? '') . "\n";
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

try {
    $db = App\Core\Database::connect();
    echo "connected\n";
    $stmt = $db->query('SHOW DATABASES');
    foreach ($stmt->fetchAll(PDO::FETCH_COLUMN) as $dbName) {
        echo $dbName . "\n";
    }
    $tables = $db->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    echo 'TABLES=' . implode(',', $tables) . "\n";
} catch (Throwable $e) {
    echo 'ERR:' . $e->getMessage() . "\n";
}

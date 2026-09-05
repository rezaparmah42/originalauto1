<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';

$result = [];
try {
    $pdo = \App\Core\Database::connect();
    $result[] = 'CONNECTED';
    $row = $pdo->query('SELECT DATABASE() AS db, USER() AS user')->fetch(PDO::FETCH_ASSOC);
    $result[] = 'DATABASE=' . ($row['db'] ?? '');
    $result[] = 'USER=' . ($row['user'] ?? '');
    $result[] = '';
    $result[] = 'SHOW DATABASES LIKE \'original_east\';';
    $stmt = $pdo->query("SHOW DATABASES LIKE 'original_east'");
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $result[] = 'DB:' . implode('|', $r);
    }
    $result[] = '';
    $result[] = 'TABLES CHECK';
    $tables = ['users','customers','articles','products','services','invoices','payments','repairs','workshop_tasks','notifications'];
    foreach ($tables as $table) {
        $stmt = $pdo->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'original_east' AND TABLE_NAME = '" . $table . "'");
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);
        $result[] = $table . ':' . ($exists ? 'FOUND' : 'MISSING');
    }
    $stmt = $pdo->query("SELECT COUNT(*) AS count_tables FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'original_east'");
    $count = $stmt->fetch(PDO::FETCH_ASSOC);
    $result[] = '';
    $result[] = 'TABLE_COUNT=' . ($count['count_tables'] ?? '0');
    $result[] = '';
    $result[] = 'SHOW TABLES FROM original_east';
    $stmt = $pdo->query('SHOW TABLES FROM original_east');
    while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
        $result[] = $row[0];
    }
} catch (Throwable $e) {
    $result[] = 'ERROR: ' . $e->getMessage();
}
file_put_contents(__DIR__ . '/tmp_db_verify_output.txt', implode("\n", $result));

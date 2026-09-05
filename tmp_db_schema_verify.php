<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';

try {
    $pdo = \App\Core\Database::connect();
    echo "CONNECTED\n";
    $row = $pdo->query('SELECT DATABASE() AS db, USER() AS user')->fetch(PDO::FETCH_ASSOC);
    echo 'DB=' . ($row['db'] ?? '') . "\n";
    echo 'USER=' . ($row['user'] ?? '') . "\n";

    $stmt = $pdo->query("SHOW DATABASES LIKE 'original_east'");
    $dbExists = (bool) $stmt->fetch(PDO::FETCH_ASSOC);
    echo 'DATABASE_EXISTS=' . ($dbExists ? 'YES' : 'NO') . "\n";

    if ($dbExists) {
        $important = ['users','customers','articles','products','services','invoices','payments','repairs','workshop_tasks','notifications'];
        $placeholders = implode(',', array_fill(0, count($important), '?'));
        $stmt = $pdo->prepare("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'original_east' AND TABLE_NAME IN ($placeholders)");
        $stmt->execute($important);
        $found = $stmt->fetchAll(PDO::FETCH_COLUMN);
        $found = $found ?: [];
        echo "IMPORTANT_TABLES:\n";
        foreach ($important as $table) {
            echo $table . ':' . (in_array($table, $found, true) ? 'FOUND' : 'MISSING') . "\n";
        }

        $stmt = $pdo->query("SELECT COUNT(*) AS count_tables FROM information_schema.TABLES WHERE TABLE_SCHEMA = 'original_east'");
        $count = $stmt->fetch(PDO::FETCH_ASSOC);
        echo 'TABLE_COUNT=' . ($count['count_tables'] ?? '0') . "\n";

        echo "TABLE_LIST:\n";
        $stmt = $pdo->query('SHOW TABLES FROM original_east');
        while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
            echo $row[0] . "\n";
        }
    }
} catch (Throwable $e) {
    echo 'ERROR: ' . $e->getMessage() . "\n";
    exit(1);
}

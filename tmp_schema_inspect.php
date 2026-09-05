<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';

$pdo = \App\Core\Database::connect();

$tables = ['articles','customers'];
foreach ($tables as $table) {
    $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
    $row = $stmt->fetch(PDO::FETCH_NUM);
    echo $table . ':' . ($row ? 'FOUND' : 'MISSING') . PHP_EOL;
    if ($row) {
        $cols = $pdo->query("SHOW COLUMNS FROM `$table`");
        while ($col = $cols->fetch(PDO::FETCH_ASSOC)) {
            echo '  ' . $col['Field'] . ' ' . $col['Type'] . ' ' . ($col['Null'] === 'YES' ? 'NULL' : 'NOT NULL') . PHP_EOL;
        }
    }
}

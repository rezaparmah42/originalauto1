<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::connect();
$tables = ['admins', 'vehicle_models'];
foreach ($tables as $t) {
    $r = $db->query('SHOW TABLES LIKE ' . $db->quote($t));
    if ($r && $r->fetchColumn()) {
        echo "TABLE $t exists\n";
        foreach ($db->query('SHOW COLUMNS FROM ' . $t) as $c) {
            echo $c['Field'] . ' ' . $c['Type'] . ' ' . ($c['Null'] === 'NO' ? 'NOT NULL' : 'NULL') . "\n";
        }
    } else {
        echo "TABLE $t missing\n";
    }
    echo "\n";
}

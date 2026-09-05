<?php
require __DIR__ . '/config/config.php';
require __DIR__ . '/app/Core/Database.php';

$pdo = \App\Core\Database::connect();
$sql = file_get_contents(__DIR__ . '/database/obd2_migration.sql');
$pdo->exec($sql);
echo "applied\n";

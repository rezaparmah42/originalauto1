<?php
require __DIR__ . '/../config/config.php';
$outFile = __DIR__ . '/../phase1_db_schema.txt';
$dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    file_put_contents($outFile, "CONNECT_ERROR: " . $e->getMessage());
    echo "WROTE: $outFile\n";
    exit(1);
}
$schema = [];
$tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);
foreach ($tables as $t) {
    $cols = $pdo->query("SHOW FULL COLUMNS FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
    $indexes = $pdo->query("SHOW INDEX FROM `$t`")->fetchAll(PDO::FETCH_ASSOC);
    $foreign = [];
    // attempt to get foreign keys from information_schema
    $fks = $pdo->prepare("SELECT CONSTRAINT_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND REFERENCED_TABLE_NAME IS NOT NULL");
    $fks->execute([DB_NAME, $t]);
    $fks = $fks->fetchAll(PDO::FETCH_ASSOC);
    $schema[$t] = ['columns' => $cols, 'indexes' => $indexes, 'foreign_keys' => $fks];
}
file_put_contents($outFile, json_encode(['database'=>DB_NAME,'host'=>DB_HOST,'tables'=>$schema], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "WROTE: $outFile\n";

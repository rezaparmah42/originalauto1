<?php
require_once __DIR__ . '/../config/config.php';

$tables = [
    'vehicle_catalog','vehicle_brands','vehicle_models','vehicles',
    'products','product_compatibility','inventory','suppliers',
    'bookings','repairs','orders','order_items','payments','users'
];

$columns_to_check = ['name_fa','name_en','slug'];

$dsn = "mysql:host=" . DB_HOST . ";dbname=information_schema;charset=utf8mb4";
try {
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'DB connect failed: ' . $e->getMessage()]);
    exit(1);
}

$results = [];
foreach ($tables as $table) {
    $res = ['exists' => false, 'columns' => []];

    $stmt = $pdo->prepare("SELECT TABLE_NAME FROM TABLES WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table LIMIT 1");
    $stmt->execute([':db' => DB_NAME, ':table' => $table]);
    $row = $stmt->fetch();
    if ($row) {
        $res['exists'] = true;
        $colStmt = $pdo->prepare("SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE FROM COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :table");
        $colStmt->execute([':db' => DB_NAME, ':table' => $table]);
        $cols = [];
        foreach ($colStmt->fetchAll() as $c) {
            $cols[$c['COLUMN_NAME']] = ['type' => $c['DATA_TYPE'], 'nullable' => $c['IS_NULLABLE']];
        }
        $res['columns'] = $cols;
        foreach ($columns_to_check as $cname) {
            $res['has_'.$cname] = isset($cols[$cname]);
        }
    }

    $results[$table] = $res;
}

echo json_encode(['db' => DB_NAME, 'results' => $results], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

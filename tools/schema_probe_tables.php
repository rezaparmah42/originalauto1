<?php
require __DIR__ . '/../config/config.php';
try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Throwable $e) {
    echo json_encode(['error' => $e->getMessage()], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit(1);
}
$tables = [
    'vehicle_catalog','vehicle_brands','vehicle_models','vehicles','products','product_compatibility','inventory','suppliers','bookings','repairs','orders','order_items','payments','users','roles','permissions'
];
$out = ['db' => DB_NAME, 'results' => []];
foreach ($tables as $t) {
    $r = ['exists' => false, 'columns' => []];
    try {
        $stmt = $pdo->prepare('SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :tbl LIMIT 1');
        $stmt->execute([':db' => DB_NAME, ':tbl' => $t]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $r['exists'] = true;
            $colStmt = $pdo->prepare('SELECT COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_DEFAULT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = :db AND TABLE_NAME = :tbl ORDER BY ORDINAL_POSITION');
            $colStmt->execute([':db' => DB_NAME, ':tbl' => $t]);
            $cols = $colStmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($cols as $c) {
                $r['columns'][] = $c;
            }
        }
    } catch (Throwable $e) {
        $r['error'] = $e->getMessage();
    }
    $out['results'][$t] = $r;
}
file_put_contents(__DIR__ . '/schema_probe_output.json', json_encode($out, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo "Wrote schema_probe_output.json\n";

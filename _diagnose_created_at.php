<?php
require __DIR__ . '/config/config.php';
$db = null;
try {
    $dsn = "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4";
    $db = new PDO($dsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
} catch (Exception $e) {
    file_put_contents(__DIR__ . '/_created_at_diagnostic.json', json_encode(['error' => $e->getMessage()], JSON_PRETTY_PRINT));
    exit(1);
}
$verify = file_get_contents(__DIR__ . '/verify_customer_smart_garage.php');
$tables = [];
if (preg_match_all('/INSERT\s+INTO\s+`?([a-z0-9_]+)`?/i', $verify, $m)) {
    foreach ($m[1] as $t) {
        $tables[$t] = true;
    }
}
// Also include requiredTables if present
if (preg_match('/\$requiredTables\s*=\s*\[([^\]]+)\]/s', $verify, $m2)) {
    $list = $m2[1];
    // match identifiers possibly wrapped in single or double quotes
    if (preg_match_all('/["\']?([a-z0-9_]+)["\']?/', $list, $m3)) {
        foreach ($m3[1] as $t) {
            $tables[$t] = true;
        }
    } else {
        // fallback parse by commas
        $parts = preg_split('/,\s*/', $list);
        foreach ($parts as $p) {
            $p = trim($p, " \t\n\r\'\"");
            if ($p !== '') {
                $tables[$p] = true;
            }
        }
    }
}
$tables = array_keys($tables);
$results = [];
foreach ($tables as $t) {
    $stmt = $db->prepare("SELECT TABLE_NAME,COLUMN_NAME,COLUMN_TYPE,IS_NULLABLE,COLUMN_DEFAULT FROM information_schema.COLUMNS WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? AND COLUMN_NAME = 'created_at'");
    $stmt->execute([DB_NAME, $t]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $results[$t] = $row ?: null;
}
file_put_contents(__DIR__ . '/_created_at_diagnostic.json', json_encode(['db'=>DB_NAME,'tables'=>$results], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
echo "DIAGNOSTIC_WRITTEN\n";

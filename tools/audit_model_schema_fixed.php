<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$db = App\Core\Database::connect();
$tables = [];
$stmt = $db->query('SHOW TABLES');
while ($row = $stmt->fetch(PDO::FETCH_NUM)) {
    $tables[] = $row[0];
}

$schema = [];
foreach ($tables as $table) {
    $stmt = $db->query('SHOW COLUMNS FROM `' . str_replace('`', '``', $table) . '`');
    $schema[$table] = [];
    while ($col = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $schema[$table][] = $col['Field'];
    }
}

$models = glob(__DIR__ . '/../app/Models/*.php');
$issues = [];
foreach ($models as $file) {
    $content = file_get_contents($file);
    if ($content === false) {
        continue;
    }
    preg_match_all('/\b(?:FROM|JOIN|INTO|UPDATE|DELETE FROM)\s+`?([a-zA-Z0-9_]+)`?/i', $content, $matches);
    foreach (array_unique($matches[1]) as $table) {
        if (!isset($schema[$table])) {
            $issues[] = [
                'file' => $file,
                'problem' => "Missing table: $table",
            ];
        }
    }
    preg_match_all('/\b([a-zA-Z0-9_]+)\.([a-zA-Z0-9_]+)/', $content, $matches);
    foreach ($matches[1] as $idx => $tbl) {
        $col = $matches[2][$idx];
        if (isset($schema[$tbl]) && !in_array($col, $schema[$tbl], true)) {
            $issues[] = [
                'file' => $file,
                'problem' => "Missing column: $tbl.$col",
            ];
        }
    }
}

if (empty($issues)) {
    echo "NO SCHEMA ISSUES FOUND\n";
    exit(0);
}

foreach ($issues as $issue) {
    echo "FILE: " . basename($issue['file']) . "\n";
    echo "PROBLEM: " . $issue['problem'] . "\n";
    echo "---\n";
}

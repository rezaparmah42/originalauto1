<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::connect();
$migrations = [
    'workshop_management_migration.sql',
    'vehicle_intelligence_migration.sql',
    'obd2_migration.sql',
    'ai_diagnostic_migration.sql',
    'api_security_fix_migration.sql',
    'notification_system_migration.sql',
    'ecommerce_migration.sql',
];
foreach ($migrations as $mig) {
    echo "--- $mig ---\n";
    $path = __DIR__ . '/../database/' . $mig;
    if (!file_exists($path)) {
        echo "File missing: $path\n";
        continue;
    }
    $sql = file_get_contents($path);
    preg_match_all('/CREATE TABLE IF NOT EXISTS\s+`?([a-zA-Z0-9_]+)`?/i', $sql, $matches);
    $tables = array_unique($matches[1]);
    if (empty($tables)) {
        echo "No tables found in migration.\n";
        continue;
    }
    foreach ($tables as $table) {
        $r = $db->query('SHOW TABLES LIKE ' . $db->quote($table));
        $exists = $r && $r->fetchColumn() ? 'exists' : 'missing';
        echo "$table: $exists\n";
    }
    echo "\n";
}

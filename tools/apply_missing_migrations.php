<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
$db = App\Core\Database::connect();
$migrationFiles = [
    'workshop_management_migration.sql',
    'vehicle_intelligence_migration.sql',
    'obd2_migration.sql',
    'ai_diagnostic_migration.sql',
    'notification_system_migration.sql',
];
foreach ($migrationFiles as $file) {
    $path = __DIR__ . '/../database/' . $file;
    if (!file_exists($path)) {
        echo "Missing migration file: $path\n";
        continue;
    }
    echo "Applying $file\n";
    $sql = file_get_contents($path);
    $parts = preg_split('/;\s*\n/', $sql);
    foreach ($parts as $part) {
        $part = trim($part);
        if ($part === '') {
            continue;
        }
        try {
            $db->exec($part);
            echo "  OK: " . substr($part, 0, 80) . "\n";
        } catch (PDOException $e) {
            echo "  SKIP/ERROR: " . $e->getMessage() . "\n";
        }
    }
    echo "\n";
}

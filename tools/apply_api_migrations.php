<?php
require_once __DIR__ . '/../config/config.php';
if (!defined('PROJECT_ACCESS')) define('PROJECT_ACCESS', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::connect();
$sql = file_get_contents(__DIR__ . '/../database/api_security_fix_migration.sql');
if ($sql === false) {
    echo "Migration file not found\n";
    exit(1);
}

// Split statements by semicolon followed by newline for safety
$parts = preg_split('/;\s*\n/', $sql);
foreach ($parts as $part) {
    $part = trim($part);
    if (empty($part)) continue;
    try {
        $db->exec($part);
        echo "Executed statement.\n";
    } catch (PDOException $e) {
        echo "Statement failed: " . $e->getMessage() . "\n";
    }
}

// Verify
$tables = ['api_tokens','api_devices','api_logs'];
foreach ($tables as $t) {
    $res = $db->query("SHOW TABLES LIKE '" . $t . "'")->fetch();
    echo $t . ': ' . ($res ? 'CREATED' : 'MISSING') . PHP_EOL;
}

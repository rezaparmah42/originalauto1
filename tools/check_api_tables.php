<?php
require_once __DIR__ . '/../config/config.php';
if (!defined('PROJECT_ACCESS')) define('PROJECT_ACCESS', true);
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../app/Core/Database.php';

use App\Core\Database;

$db = Database::connect();
$tables = ['api_tokens','api_devices','api_logs'];
foreach ($tables as $t) {
    $q = "SHOW TABLES LIKE '" . str_replace("'","\\'", $t) . "'";
    $stmt = $db->query($q);
    $exists = $stmt && $stmt->fetch();
    echo $t . ': ' . ($exists ? 'FOUND' : 'MISSING') . PHP_EOL;
}

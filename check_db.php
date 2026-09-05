<?php
require 'config/config.php';
require 'app/Core/Database.php';

$db = \App\Core\Database::connect();
$stmt = $db->query("SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_SCHEMA = '" . DB_NAME . "'");
$tables = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo "Database: " . DB_NAME . "\n";
echo "Tables found: " . count($tables) . "\n\n";

if (empty($tables)) {
    echo "No tables in database.\n";
    echo "Run production_schema.sql to create tables.\n";
} else {
    foreach ($tables as $t) {
        echo "  - " . $t['TABLE_NAME'] . "\n";
    }
}

<?php

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/MigrationManager.php';

$manager = new App\Core\MigrationManager();
$status = $manager->status();

echo "Migration status\n";
if (empty($status)) {
    echo "No migration files found in database/migrations.\n";
    exit(0);
}

foreach ($status as $migration) {
    echo sprintf("- %s: %s\n", $migration['name'], $migration['status']);
}

exit(0);

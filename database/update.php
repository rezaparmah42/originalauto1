<?php

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server') {
    http_response_code(403);
    echo "ERROR: Database updater is CLI-only.\n";
    exit(1);
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/MigrationManager.php';

try {
    $pdo = App\Core\Database::connect();
    $manager = new App\Core\MigrationManager($pdo);
    $pending = $manager->getPendingMigrations();

    if (empty($pending)) {
        echo "ALREADY_INSTALLED\n";
        exit(0);
    }

    $manager->run();
    echo "MIGRATION_COMPLETE\n";
    exit(0);
} catch (Throwable $e) {
    error_log('[update.php] ' . $e->getMessage());
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

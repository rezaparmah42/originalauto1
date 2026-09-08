<?php

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server') {
    http_response_code(403);
    echo 'Forbidden';
    exit;
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/MigrationManager.php';

try {
    $manager = new App\Core\MigrationManager();
    $result = $manager->run();

    if (empty($result)) {
        echo "No pending migrations.\n";
        exit(0);
    }

    foreach ($result as $migration) {
        $message = $migration['status'] === 'applied' ? 'APPLIED' : strtoupper((string) $migration['status']);
        echo sprintf("[%s] %s\n", $message, $migration['name']);
        if (!empty($migration['error'])) {
            echo 'Error: ' . $migration['error'] . "\n";
        }
    }

    exit(0);
} catch (Throwable $e) {
    error_log('[run_migrations] ' . $e->getMessage());
    echo 'Migration failed: ' . $e->getMessage() . "\n";
    exit(1);
}

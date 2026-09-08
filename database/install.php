<?php

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server') {
    http_response_code(403);
    echo "ERROR: Database installer is CLI-only.\n";
    exit(1);
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/MigrationManager.php';

function databaseHasAnyTables(PDO $pdo, string $dbName): bool
{
    $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :database";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':database' => $dbName]);
    return (int) $stmt->fetchColumn() > 0;
}

function runSqlFile(PDO $pdo, string $path): void
{
    if (!is_file($path)) {
        throw new RuntimeException("SQL file not found: {$path}");
    }

    $sql = file_get_contents($path);
    if ($sql === false) {
        throw new RuntimeException("Unable to read SQL file: {$path}");
    }

    $sql = preg_replace('/\/\*.*?\*\//s', '', $sql);
    $sql = preg_replace('/--.*$/m', '', $sql);
    $statements = array_filter(array_map('trim', preg_split('/;\s*(?:\r?\n|$)/', $sql) ?: []), static fn ($statement) => $statement !== '');

    foreach ($statements as $statement) {
        if ($statement === '') {
            continue;
        }

        try {
            $pdo->exec($statement);
        } catch (Throwable $e) {
            throw new RuntimeException("SQL execution failed in {$path}: " . $e->getMessage(), 0, $e);
        }
    }
}

function findSchemaFile(): string
{
    $candidates = [
        __DIR__ . '/schema.sql',
        __DIR__ . '/production_schema.sql',
        __DIR__ . '/original_east.sql',
    ];

    foreach ($candidates as $candidate) {
        if (is_file($candidate)) {
            return $candidate;
        }
    }

    throw new RuntimeException('No schema file found in database/');
}

function runSeedFiles(PDO $pdo): void
{
    $files = [];
    $rootSeed = __DIR__ . '/seed.sql';
    if (is_file($rootSeed)) {
        $files[] = $rootSeed;
    }

    if (is_dir(__DIR__ . '/seeds')) {
        $seedFiles = glob(__DIR__ . '/seeds/*.sql');
        if ($seedFiles !== false) {
            foreach ($seedFiles as $seedFile) {
                $files[] = $seedFile;
            }
        }
    }

    sort($files, SORT_STRING);
    foreach ($files as $file) {
        runSqlFile($pdo, $file);
    }
}

try {
    $serverDsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $dbName = DB_NAME;
    $serverPdo->exec('CREATE DATABASE IF NOT EXISTS `' . str_replace('`', '``', $dbName) . '` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');

    $dbDsn = 'mysql:host=' . DB_HOST . ';dbname=' . $dbName . ';charset=utf8mb4';
    $pdo = new PDO($dbDsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    if (databaseHasAnyTables($pdo, $dbName)) {
        echo "ALREADY_INSTALLED\n";
        exit(0);
    }

    $schemaFile = findSchemaFile();
    runSqlFile($pdo, $schemaFile);

    $manager = new App\Core\MigrationManager($pdo);
    $manager->ensureMigrationsTable();

    if (is_file(__DIR__ . '/seed.sql') || is_dir(__DIR__ . '/seeds')) {
        runSeedFiles($pdo);
    }

    echo "INSTALL_OK\n";
    exit(0);
} catch (Throwable $e) {
    error_log('[install.php] ' . $e->getMessage());
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

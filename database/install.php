<?php

if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'cli-server') {
    http_response_code(403);
    echo "ERROR: Database installer is CLI-only.\n";
    exit(1);
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';
require_once __DIR__ . '/../app/Core/MigrationManager.php';

function databaseExists(PDO $pdo, string $dbName): bool
{
    $sql = 'SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = :database';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':database' => $dbName]);
    return $stmt->fetchColumn() !== false;
}

function databaseHasAnyTables(PDO $pdo, string $dbName): bool
{
    $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = :database";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([':database' => $dbName]);
    return (int) $stmt->fetchColumn() > 0;
}

function normalizeSchemaSql(string $sql): string
{
    $sql = preg_replace('/^\s*CREATE\s+DATABASE\s+IF\s+NOT\s+EXISTS\s+`?[^;]+`?\s*.*?;/im', '', $sql ?? '');
    $sql = preg_replace('/^\s*USE\s+`?[^;]+`?\s*;/im', '', (string) $sql);
    $sql = preg_replace('/\/\*.*?\*\//s', '', (string) $sql);
    $sql = preg_replace('/--.*$/m', '', (string) $sql);
    return trim((string) $sql);
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

    $sql = normalizeSchemaSql($sql);
    if (trim($sql) === '') {
        return;
    }

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
    $dbName = DB_NAME;
    $serverDsn = 'mysql:host=' . DB_HOST . ';charset=utf8mb4';
    $serverPdo = new PDO($serverDsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    if (!databaseExists($serverPdo, $dbName)) {
        echo "DATABASE_MUST_EXIST\n";
        exit(1);
    }

    $dbDsn = 'mysql:host=' . DB_HOST . ';dbname=' . $dbName . ';charset=utf8mb4';
    $pdo = new PDO($dbDsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $pdo->exec('SET NAMES utf8mb4');
    $pdo->exec('SET CHARACTER SET utf8mb4');

    if (databaseHasAnyTables($pdo, $dbName)) {
        echo "ALREADY_INSTALLED\n";
        exit(0);
    }

    $schemaFile = findSchemaFile();
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 0');
    runSqlFile($pdo, $schemaFile);
    $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');

    $manager = new App\Core\MigrationManager($pdo);
    $manager->ensureMigrationsTable();

    if (is_file(__DIR__ . '/seed.sql') || is_dir(__DIR__ . '/seeds')) {
        runSeedFiles($pdo);
    }

    echo "INSTALL_OK\n";
    exit(0);
} catch (Throwable $e) {
    try {
        if (isset($pdo) && $pdo instanceof PDO) {
            $pdo->exec('SET FOREIGN_KEY_CHECKS = 1');
        }
    } catch (Throwable $ignored) {
    }

    error_log('[install.php] ' . $e->getMessage());
    echo "ERROR: " . $e->getMessage() . "\n";
    exit(1);
}

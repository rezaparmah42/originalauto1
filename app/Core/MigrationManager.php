<?php

namespace App\Core;

use PDO;
use Throwable;

class MigrationManager
{
    private PDO $db;
    private string $migrationsDir;

    public function __construct(?PDO $db = null, ?string $migrationsDir = null)
    {
        $this->db = $db ?? Database::connect();
        $this->migrationsDir = $migrationsDir ?? dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'database' . DIRECTORY_SEPARATOR . 'migrations';
    }

    public function ensureMigrationsTable(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `database_migrations` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration_name` VARCHAR(255) NOT NULL,
            `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `uniq_database_migration_name` (`migration_name`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

        $this->db->exec($sql);
    }

    public function listMigrations(): array
    {
        if (!is_dir($this->migrationsDir)) {
            return [];
        }

        $files = glob($this->migrationsDir . DIRECTORY_SEPARATOR . '*.sql');
        if ($files === false) {
            return [];
        }

        $migrations = [];
        foreach ($files as $file) {
            $name = basename($file);
            $migrations[] = [
                'name' => $name,
                'path' => $file,
            ];
        }

        usort($migrations, static function (array $a, array $b): int {
            return strcmp($a['name'], $b['name']);
        });

        return $migrations;
    }

    public function getExecutedMigrations(): array
    {
        $executed = [];

        try {
            $statement = $this->db->query('SELECT migration_name FROM database_migrations ORDER BY id ASC');
            if ($statement === false) {
                return $executed;
            }

            foreach ($statement as $row) {
                $name = trim((string) ($row['migration_name'] ?? ''));
                if ($name !== '') {
                    $executed[$name] = true;
                }
            }
        } catch (Throwable $e) {
            error_log('[MigrationManager] Could not read executed migrations: ' . $e->getMessage());
        }

        return $executed;
    }

    public function getPendingMigrations(): array
    {
        $this->ensureMigrationsTable();

        $executed = $this->getExecutedMigrations();
        $pending = [];

        foreach ($this->listMigrations() as $migration) {
            $name = (string) ($migration['name'] ?? '');
            if ($name !== '' && !isset($executed[$name])) {
                $pending[] = $migration;
            }
        }

        return $pending;
    }

    public function run(): array
    {
        $this->ensureMigrationsTable();

        $results = [];
        foreach ($this->getPendingMigrations() as $migration) {
            $name = (string) ($migration['name'] ?? '');
            $path = (string) ($migration['path'] ?? '');

            if ($name === '' || $path === '' || !is_file($path)) {
                continue;
            }

            $sql = file_get_contents($path);
            if ($sql === false || trim($sql) === '') {
                $results[] = ['name' => $name, 'status' => 'skipped', 'reason' => 'empty file'];
                continue;
            }

            try {
                $this->db->exec($sql);
                $insert = $this->db->prepare('INSERT INTO database_migrations (migration_name, executed_at) VALUES (:name, NOW())');
                $insert->execute([':name' => $name]);

                $results[] = ['name' => $name, 'status' => 'applied'];
            } catch (Throwable $e) {
                $results[] = ['name' => $name, 'status' => 'failed', 'error' => $e->getMessage()];
                throw $e;
            }
        }

        return $results;
    }

    public function status(): array
    {
        $this->ensureMigrationsTable();
        $exists = $this->getExecutedMigrations();

        $rows = [];
        foreach ($this->listMigrations() as $migration) {
            $name = (string) ($migration['name'] ?? '');
            $rows[] = [
                'name' => $name,
                'status' => isset($exists[$name]) ? 'applied' : 'pending',
            ];
        }

        return $rows;
    }
}

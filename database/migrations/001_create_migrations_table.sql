CREATE TABLE IF NOT EXISTS `database_migrations` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `migration_name` VARCHAR(255) NOT NULL,
    `executed_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uniq_database_migration_name` (`migration_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

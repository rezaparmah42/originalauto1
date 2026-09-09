-- Phase 2: vehicle catalog variants. This migration is schema-only by design.
CREATE TABLE IF NOT EXISTS `vehicle_variants` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `model_id` INT NOT NULL,
    `name_fa` VARCHAR(180) NOT NULL,
    `name_en` VARCHAR(180) DEFAULT NULL,
    `slug` VARCHAR(191) NOT NULL,
    `engine_code` VARCHAR(120) DEFAULT NULL,
    `engine_type` VARCHAR(120) DEFAULT NULL,
    `fuel_type` VARCHAR(80) DEFAULT NULL,
    `transmission` VARCHAR(80) DEFAULT NULL,
    `year_from` SMALLINT UNSIGNED DEFAULT NULL,
    `year_to` SMALLINT UNSIGNED DEFAULT NULL,
    `description_fa` TEXT DEFAULT NULL,
    `description_en` TEXT DEFAULT NULL,
    `seo_title_fa` VARCHAR(200) DEFAULT NULL,
    `seo_description_fa` TEXT DEFAULT NULL,
    `search_keywords_fa` TEXT DEFAULT NULL,
    `status` TINYINT NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uq_vehicle_variants_model_slug` (`model_id`, `slug`),
    KEY `idx_vehicle_variants_model_id` (`model_id`),
    KEY `idx_vehicle_variants_slug` (`slug`),
    KEY `idx_vehicle_variants_status` (`status`),
    KEY `idx_vehicle_variants_year_range` (`year_from`, `year_to`),
    CONSTRAINT `fk_vehicle_variants_model_id`
        FOREIGN KEY (`model_id`) REFERENCES `vehicle_models` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

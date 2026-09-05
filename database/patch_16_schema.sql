-- Patch 16 schema repair for Original Shargh
-- Purpose: create or align the articles table with the current app contract
-- This script is conservative and idempotent.

USE original_east;

CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_fa VARCHAR(220) NOT NULL,
    title_en VARCHAR(220) DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    category VARCHAR(120) DEFAULT NULL,
    author VARCHAR(120) DEFAULT NULL,
    content_fa LONGTEXT DEFAULT NULL,
    content_en LONGTEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    seo_title_fa VARCHAR(200) DEFAULT NULL,
    seo_title_en VARCHAR(200) DEFAULT NULL,
    seo_description_fa TEXT DEFAULT NULL,
    seo_description_en TEXT DEFAULT NULL,
    meta_description_fa TEXT DEFAULT NULL,
    meta_description_en TEXT DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_articles_slug (slug),
    KEY idx_articles_category (category),
    KEY idx_articles_status (status),
    KEY idx_articles_created_at (created_at),
    FULLTEXT KEY ft_articles_content (title_fa, content_fa, search_keywords_fa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE articles
    ADD COLUMN IF NOT EXISTS title_fa VARCHAR(220) NOT NULL AFTER id,
    ADD COLUMN IF NOT EXISTS title_en VARCHAR(220) DEFAULT NULL AFTER title_fa,
    ADD COLUMN IF NOT EXISTS slug VARCHAR(191) NOT NULL AFTER title_en,
    ADD COLUMN IF NOT EXISTS category VARCHAR(120) DEFAULT NULL AFTER slug,
    ADD COLUMN IF NOT EXISTS author VARCHAR(120) DEFAULT NULL AFTER category,
    ADD COLUMN IF NOT EXISTS content_fa LONGTEXT DEFAULT NULL AFTER author,
    ADD COLUMN IF NOT EXISTS content_en LONGTEXT DEFAULT NULL AFTER content_fa,
    ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL AFTER content_en,
    ADD COLUMN IF NOT EXISTS seo_title_fa VARCHAR(200) DEFAULT NULL AFTER image,
    ADD COLUMN IF NOT EXISTS seo_title_en VARCHAR(200) DEFAULT NULL AFTER seo_title_fa,
    ADD COLUMN IF NOT EXISTS seo_description_fa TEXT DEFAULT NULL AFTER seo_title_en,
    ADD COLUMN IF NOT EXISTS seo_description_en TEXT DEFAULT NULL AFTER seo_description_fa,
    ADD COLUMN IF NOT EXISTS meta_description_fa TEXT DEFAULT NULL AFTER seo_description_en,
    ADD COLUMN IF NOT EXISTS meta_description_en TEXT DEFAULT NULL AFTER meta_description_fa,
    ADD COLUMN IF NOT EXISTS search_keywords_fa TEXT DEFAULT NULL AFTER meta_description_en,
    ADD COLUMN IF NOT EXISTS search_keywords_en TEXT DEFAULT NULL AFTER search_keywords_fa,
    ADD COLUMN IF NOT EXISTS status TINYINT DEFAULT 1 AFTER search_keywords_en,
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER status;

ALTER TABLE articles
    ADD UNIQUE INDEX IF NOT EXISTS uq_articles_slug (slug),
    ADD INDEX IF NOT EXISTS idx_articles_category (category),
    ADD INDEX IF NOT EXISTS idx_articles_status (status),
    ADD INDEX IF NOT EXISTS idx_articles_created_at (created_at);

-- Fulltext index is added only if missing.
SET @idx_exists := (SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = DATABASE() AND table_name = 'articles' AND index_name = 'ft_articles_content');
SET @stmt := IF(@idx_exists = 0, 'ALTER TABLE articles ADD FULLTEXT KEY ft_articles_content (title_fa, content_fa, search_keywords_fa)', 'SELECT 1');
PREPARE stmt FROM @stmt;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

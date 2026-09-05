<?php
// Safe migration runner for PATCH_38
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$db = \App\Core\Database::connect();

function tableExists($db, $table) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = ? AND table_name = ?");
    $stmt->execute([DB_NAME, $table]);
    return (int)$stmt->fetchColumn() > 0;
}

function columnExists($db, $table, $column) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.columns WHERE table_schema = ? AND table_name = ? AND column_name = ?");
    $stmt->execute([DB_NAME, $table, $column]);
    return (int)$stmt->fetchColumn() > 0;
}

function indexExists($db, $table, $indexName) {
    $stmt = $db->prepare("SELECT COUNT(*) FROM information_schema.statistics WHERE table_schema = ? AND table_name = ? AND index_name = ?");
    $stmt->execute([DB_NAME, $table, $indexName]);
    return (int)$stmt->fetchColumn() > 0;
}

try {
    // Add product columns if missing
    if (!columnExists($db, 'products', 'category_id')) {
        $db->exec("ALTER TABLE products ADD COLUMN category_id INT DEFAULT NULL");
        echo "Added column products.category_id\n";
    }
    if (!columnExists($db, 'products', 'featured')) {
        $db->exec("ALTER TABLE products ADD COLUMN featured TINYINT DEFAULT 0");
        echo "Added column products.featured\n";
    }
    if (!columnExists($db, 'products', 'views_count')) {
        $db->exec("ALTER TABLE products ADD COLUMN views_count INT DEFAULT 0");
        echo "Added column products.views_count\n";
    }
    if (!columnExists($db, 'products', 'seo_title_fa')) {
        $db->exec("ALTER TABLE products ADD COLUMN seo_title_fa VARCHAR(200) DEFAULT NULL");
        echo "Added column products.seo_title_fa\n";
    }
    if (!columnExists($db, 'products', 'seo_description_fa')) {
        $db->exec("ALTER TABLE products ADD COLUMN seo_description_fa TEXT DEFAULT NULL");
        echo "Added column products.seo_description_fa\n";
    }
    if (!columnExists($db, 'products', 'search_keywords_fa')) {
        $db->exec("ALTER TABLE products ADD COLUMN search_keywords_fa TEXT DEFAULT NULL");
        echo "Added column products.search_keywords_fa\n";
    }

    // Create categories table
    if (!tableExists($db, 'product_categories')) {
        $db->exec(<<<'SQL'
CREATE TABLE product_categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name_fa VARCHAR(200) NOT NULL,
  name_en VARCHAR(200) DEFAULT NULL,
  slug VARCHAR(191) NOT NULL,
  description TEXT DEFAULT NULL,
  status TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY uq_product_categories_slug (slug),
  KEY idx_product_categories_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        echo "Created table product_categories\n";
    } else {
        echo "Table product_categories already exists\n";
    }

    // Create product_images table
    if (!tableExists($db, 'product_images')) {
        $db->exec(<<<'SQL'
CREATE TABLE product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) DEFAULT NULL,
  display_order INT DEFAULT 0,
  status TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_product_images_product_id (product_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
SQL
        );
        // add FK if products table exists
        if (tableExists($db, 'products')) {
            try {
                $db->exec("ALTER TABLE product_images ADD CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE");
                echo "Added FK fk_product_images_product\n";
            } catch (\Throwable $e) {
                echo "Warning: could not add FK fk_product_images_product: " . $e->getMessage() . "\n";
            }
        }
        echo "Created table product_images\n";
    } else {
        echo "Table product_images already exists\n";
    }

    // Indexes
    if (!indexExists($db, 'products', 'idx_products_category_id')) {
        try {
            $db->exec('ALTER TABLE products ADD INDEX idx_products_category_id (category_id)');
            echo "Added index idx_products_category_id\n";
        } catch (\Throwable $e) { echo "Index idx_products_category_id exists or failed: " . $e->getMessage() . "\n"; }
    }

    if (!indexExists($db, 'products', 'idx_products_featured')) {
        try {
            $db->exec('ALTER TABLE products ADD INDEX idx_products_featured (featured)');
            echo "Added index idx_products_featured\n";
        } catch (\Throwable $e) { echo "Index idx_products_featured exists or failed: " . $e->getMessage() . "\n"; }
    }

    if (!indexExists($db, 'products', 'idx_products_status')) {
        // products likely already has this index, skip if exists
        echo "Index idx_products_status exists or was pre-existing\n";
    }

    if (!indexExists($db, 'products', 'uq_products_slug')) {
        try {
            $db->exec('ALTER TABLE products ADD UNIQUE INDEX uq_products_slug (slug)');
            echo "Added unique index uq_products_slug\n";
        } catch (\Throwable $e) { echo "Unique index uq_products_slug exists or failed: " . $e->getMessage() . "\n"; }
    }

    echo "PATCH_38 migration applied (or already present).\n";
} catch (\Throwable $e) {
    echo "Migration error: " . $e->getMessage() . "\n";
}

return 0;

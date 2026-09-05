<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$db = App\Core\Database::connect();

function tableExists(PDO $db, string $table): bool
{
    $stmt = $db->query('SHOW TABLES LIKE ' . $db->quote($table));
    return $stmt ? (bool) $stmt->fetchColumn() : false;
}

function columnExists(PDO $db, string $table, string $column): bool
{
    $tableEscaped = str_replace('`', '``', $table);
    $stmt = $db->query('SHOW COLUMNS FROM `' . $tableEscaped . '` LIKE ' . $db->quote($column));
    return $stmt ? (bool) $stmt->fetchColumn() : false;
}

function addColumn(PDO $db, string $table, string $columnName, string $definition, array &$executed): void
{
    if (!columnExists($db, $table, $columnName)) {
        $db->exec('ALTER TABLE `' . str_replace('`', '``', $table) . '` ADD COLUMN ' . $definition);
        $executed[] = "added $table.$columnName";
    }
}

function indexExists(PDO $db, string $table, string $indexName): bool
{
    $stmt = $db->prepare('SHOW INDEX FROM `' . str_replace('`', '``', $table) . '`');
    $stmt->execute();
    $indexes = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($indexes as $index) {
        if (($index['Key_name'] ?? '') === $indexName) {
            return true;
        }
    }
    return false;
}

function constraintExists(PDO $db, string $table, string $constraintName): bool
{
    $stmt = $db->prepare('SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?');
    $stmt->execute([$table, $constraintName]);
    return (bool) $stmt->fetchColumn();
}

$executed = [];

if (!tableExists($db, 'vehicle_brands')) {
    $db->exec("CREATE TABLE IF NOT EXISTS vehicle_brands (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) DEFAULT NULL, name_fa VARCHAR(150) DEFAULT NULL, name_en VARCHAR(150) DEFAULT NULL, slug VARCHAR(120) DEFAULT NULL, category VARCHAR(80) DEFAULT NULL, status TINYINT DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $executed[] = 'created vehicle_brands';
}

if (!tableExists($db, 'technicians')) {
    $db->exec("CREATE TABLE IF NOT EXISTS technicians (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT NULL, name VARCHAR(191) NOT NULL, phone VARCHAR(50) DEFAULT NULL, specialization VARCHAR(191) DEFAULT NULL, status VARCHAR(50) DEFAULT 'active', created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $executed[] = 'created technicians';
}

if (tableExists($db, 'repairs')) {
    addColumn($db, 'repairs', 'technician_id', 'technician_id INT DEFAULT NULL AFTER booking_id', $executed);
    if (!indexExists($db, 'repairs', 'idx_repairs_technician_id')) {
        $db->exec('ALTER TABLE repairs ADD INDEX idx_repairs_technician_id (technician_id)');
        $executed[] = 'added repairs idx_repairs_technician_id';
    }
    if (tableExists($db, 'technicians') && !constraintExists($db, 'repairs', 'fk_repairs_technician_id')) {
        try {
            $db->exec('ALTER TABLE repairs ADD CONSTRAINT fk_repairs_technician_id FOREIGN KEY (technician_id) REFERENCES technicians(id) ON DELETE SET NULL ON UPDATE CASCADE');
            $executed[] = 'added repairs fk_repairs_technician_id';
        } catch (PDOException $e) {
            $executed[] = 'skipped repairs fk_repairs_technician_id: ' . $e->getMessage();
        }
    }
}

if (tableExists($db, 'vehicles')) {
    addColumn($db, 'vehicles', 'brand_id', 'brand_id INT DEFAULT NULL AFTER user_id', $executed);
    addColumn($db, 'vehicles', 'brand', 'brand VARCHAR(100) DEFAULT NULL AFTER brand_id', $executed);
    if (!indexExists($db, 'vehicles', 'idx_vehicles_brand_id')) {
        $db->exec('ALTER TABLE vehicles ADD INDEX idx_vehicles_brand_id (brand_id)');
        $executed[] = 'added vehicles idx_vehicles_brand_id';
    }
    if (tableExists($db, 'vehicle_brands') && !constraintExists($db, 'vehicles', 'fk_vehicles_brand_id')) {
        try {
            $db->exec('ALTER TABLE vehicles ADD CONSTRAINT fk_vehicles_brand_id FOREIGN KEY (brand_id) REFERENCES vehicle_brands(id) ON DELETE SET NULL ON UPDATE CASCADE');
            $executed[] = 'added vehicles fk_vehicles_brand_id';
        } catch (PDOException $e) {
            $executed[] = 'skipped vehicles fk_vehicles_brand_id: ' . $e->getMessage();
        }
    }
}

if (!tableExists($db, 'bookings')) {
    $db->exec("CREATE TABLE IF NOT EXISTS bookings (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        vehicle_id INT DEFAULT NULL,
        service_id INT DEFAULT NULL,
        problem TEXT NOT NULL,
        status VARCHAR(50) NOT NULL DEFAULT 'new',
        booking_date DATETIME DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_bookings_user_id (user_id),
        INDEX idx_bookings_vehicle_id (vehicle_id),
        INDEX idx_bookings_service_id (service_id),
        INDEX idx_bookings_status (status),
        INDEX idx_bookings_created_at (created_at),
        CONSTRAINT fk_bookings_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
        CONSTRAINT fk_bookings_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL ON UPDATE CASCADE,
        CONSTRAINT fk_bookings_service_id FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL ON UPDATE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $executed[] = 'created bookings';
}

if (tableExists($db, 'products')) {
    addColumn($db, 'products', 'title_fa', 'title_fa VARCHAR(200) DEFAULT NULL AFTER title', $executed);
    addColumn($db, 'products', 'title_en', 'title_en VARCHAR(200) DEFAULT NULL AFTER title_fa', $executed);
    addColumn($db, 'products', 'slug', 'slug VARCHAR(191) DEFAULT NULL AFTER title_en', $executed);
    addColumn($db, 'products', 'description_fa', 'description_fa TEXT DEFAULT NULL AFTER slug', $executed);
    addColumn($db, 'products', 'description_en', 'description_en TEXT DEFAULT NULL AFTER description_fa', $executed);
    addColumn($db, 'products', 'seo_title_fa', 'seo_title_fa VARCHAR(200) DEFAULT NULL AFTER description_en', $executed);
    addColumn($db, 'products', 'seo_title_en', 'seo_title_en VARCHAR(200) DEFAULT NULL AFTER seo_title_fa', $executed);
    addColumn($db, 'products', 'seo_description_fa', 'seo_description_fa TEXT DEFAULT NULL AFTER seo_title_en', $executed);
    addColumn($db, 'products', 'seo_description_en', 'seo_description_en TEXT DEFAULT NULL AFTER seo_description_fa', $executed);
    addColumn($db, 'products', 'search_keywords_fa', 'search_keywords_fa TEXT DEFAULT NULL AFTER seo_description_en', $executed);
    addColumn($db, 'products', 'search_keywords_en', 'search_keywords_en TEXT DEFAULT NULL AFTER search_keywords_fa', $executed);
    addColumn($db, 'products', 'stock', 'stock INT DEFAULT 0 AFTER price', $executed);

    $db->exec('UPDATE products SET title_fa = title WHERE title_fa IS NULL AND title IS NOT NULL');
    $db->exec('UPDATE products SET description_fa = description WHERE description_fa IS NULL AND description IS NOT NULL');
}

if (tableExists($db, 'services')) {
    addColumn($db, 'services', 'title_fa', 'title_fa VARCHAR(200) DEFAULT NULL AFTER title', $executed);
    addColumn($db, 'services', 'title_en', 'title_en VARCHAR(200) DEFAULT NULL AFTER title_fa', $executed);
    addColumn($db, 'services', 'description_fa', 'description_fa TEXT DEFAULT NULL AFTER description', $executed);
    addColumn($db, 'services', 'description_en', 'description_en TEXT DEFAULT NULL AFTER description_fa', $executed);
    addColumn($db, 'services', 'seo_title_fa', 'seo_title_fa VARCHAR(200) DEFAULT NULL AFTER description_en', $executed);
    addColumn($db, 'services', 'seo_title_en', 'seo_title_en VARCHAR(200) DEFAULT NULL AFTER seo_title_fa', $executed);
    addColumn($db, 'services', 'seo_description_fa', 'seo_description_fa TEXT DEFAULT NULL AFTER seo_title_en', $executed);
    addColumn($db, 'services', 'seo_description_en', 'seo_description_en TEXT DEFAULT NULL AFTER seo_description_fa', $executed);
    addColumn($db, 'services', 'search_keywords_fa', 'search_keywords_fa TEXT DEFAULT NULL AFTER seo_description_en', $executed);
    addColumn($db, 'services', 'search_keywords_en', 'search_keywords_en TEXT DEFAULT NULL AFTER search_keywords_fa', $executed);
    addColumn($db, 'services', 'price', 'price VARCHAR(60) DEFAULT NULL AFTER seo_description_en', $executed);
    addColumn($db, 'services', 'duration', 'duration VARCHAR(80) DEFAULT NULL AFTER price', $executed);

    $db->exec('UPDATE services SET title_fa = title WHERE title_fa IS NULL AND title IS NOT NULL');
    $db->exec('UPDATE services SET description_fa = description WHERE description_fa IS NULL AND description IS NOT NULL');
}

if (tableExists($db, 'vehicle_models')) {
    addColumn($db, 'vehicle_models', 'slug', 'slug VARCHAR(191) DEFAULT NULL AFTER name_fa', $executed);
    addColumn($db, 'vehicle_models', 'year_from', 'year_from INT DEFAULT NULL AFTER slug', $executed);
    addColumn($db, 'vehicle_models', 'year_to', 'year_to INT DEFAULT NULL AFTER year_from', $executed);

    $db->exec('UPDATE vehicle_models SET year_from = start_year WHERE year_from IS NULL AND start_year IS NOT NULL');
    $db->exec('UPDATE vehicle_models SET year_to = end_year WHERE year_to IS NULL AND end_year IS NOT NULL');
}

echo "Schema stabilization complete.\n";
if ($executed) {
    foreach ($executed as $item) {
        echo $item . "\n";
    }
} else {
    echo "No schema changes were required.\n";
}

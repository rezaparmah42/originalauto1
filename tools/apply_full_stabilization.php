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
    try {
        $stmt = $db->query('SHOW COLUMNS FROM `' . str_replace('`', '``', $table) . '` LIKE ' . $db->quote($column));
        return $stmt ? (bool) $stmt->fetchColumn() : false;
    } catch (PDOException $e) {
        return false;
    }
}

function indexExists(PDO $db, string $table, string $indexName): bool
{
    try {
        $stmt = $db->prepare('SHOW INDEX FROM `' . str_replace('`', '``', $table) . '`');
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (($row['Key_name'] ?? '') === $indexName) {
                return true;
            }
        }
    } catch (PDOException $e) {
        return false;
    }
    return false;
}

function constraintExists(PDO $db, string $table, string $constraintName): bool
{
    try {
        $stmt = $db->prepare('SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND CONSTRAINT_NAME = ?');
        $stmt->execute([$table, $constraintName]);
        return (bool) $stmt->fetchColumn();
    } catch (PDOException $e) {
        return false;
    }
}

function execSafe(PDO $db, string $sql, array &$executed): void
{
    try {
        $db->exec($sql);
        $executed[] = $sql;
    } catch (PDOException $e) {
        $executed[] = 'SKIPPED: ' . $sql . ' -- ' . $e->getMessage();
    }
}

function addColumn(PDO $db, string $table, string $columnName, string $definition, array &$executed): void
{
    if (!tableExists($db, $table)) {
        return;
    }
    if (!columnExists($db, $table, $columnName)) {
        execSafe($db, 'ALTER TABLE `' . str_replace('`', '``', $table) . '` ADD COLUMN ' . $definition, $executed);
    }
}

$executed = [];

// Core existing stabilization
if (!tableExists($db, 'vehicle_brands')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS vehicle_brands (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(120) DEFAULT NULL, name_fa VARCHAR(150) DEFAULT NULL, name_en VARCHAR(150) DEFAULT NULL, slug VARCHAR(120) DEFAULT NULL, category VARCHAR(80) DEFAULT NULL, status TINYINT DEFAULT 1, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'technicians')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS technicians (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT NULL, name VARCHAR(191) NOT NULL, phone VARCHAR(50) DEFAULT NULL, specialization VARCHAR(191) DEFAULT NULL, status VARCHAR(50) DEFAULT 'active', created_at DATETIME DEFAULT CURRENT_TIMESTAMP) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (tableExists($db, 'repairs')) {
    addColumn($db, 'repairs', 'technician_id', 'technician_id INT DEFAULT NULL AFTER booking_id', $executed);
    addColumn($db, 'repairs', 'booking_id', 'booking_id INT DEFAULT NULL AFTER id', $executed);
    addColumn($db, 'repairs', 'repair_notes', 'repair_notes TEXT DEFAULT NULL AFTER diagnosis', $executed);
    addColumn($db, 'repairs', 'cost', 'cost DECIMAL(12,2) DEFAULT 0 AFTER repair_notes', $executed);
    if (!indexExists($db, 'repairs', 'idx_repairs_technician_id')) {
        execSafe($db, 'ALTER TABLE repairs ADD INDEX idx_repairs_technician_id (technician_id)', $executed);
    }
    if (tableExists($db, 'technicians') && !constraintExists($db, 'repairs', 'fk_repairs_technician_id')) {
        execSafe($db, 'ALTER TABLE repairs ADD CONSTRAINT fk_repairs_technician_id FOREIGN KEY (technician_id) REFERENCES technicians(id) ON DELETE SET NULL ON UPDATE CASCADE', $executed);
    }
}

if (tableExists($db, 'vehicles')) {
    addColumn($db, 'vehicles', 'brand_id', 'brand_id INT DEFAULT NULL AFTER user_id', $executed);
    addColumn($db, 'vehicles', 'brand', 'brand VARCHAR(100) DEFAULT NULL AFTER brand_id', $executed);
    addColumn($db, 'vehicles', 'model', 'model VARCHAR(120) DEFAULT NULL AFTER brand', $executed);
    addColumn($db, 'vehicles', 'engine', 'engine VARCHAR(100) DEFAULT NULL AFTER year', $executed);
    if (!indexExists($db, 'vehicles', 'idx_vehicles_brand_id')) {
        execSafe($db, 'ALTER TABLE vehicles ADD INDEX idx_vehicles_brand_id (brand_id)', $executed);
    }
    if (tableExists($db, 'vehicle_brands') && !constraintExists($db, 'vehicles', 'fk_vehicles_brand_id')) {
        execSafe($db, 'ALTER TABLE vehicles ADD CONSTRAINT fk_vehicles_brand_id FOREIGN KEY (brand_id) REFERENCES vehicle_brands(id) ON DELETE SET NULL ON UPDATE CASCADE', $executed);
    }
}

if (!tableExists($db, 'bookings')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS bookings (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, vehicle_id INT DEFAULT NULL, service_id INT DEFAULT NULL, problem TEXT NOT NULL, status VARCHAR(50) NOT NULL DEFAULT 'new', booking_date DATETIME DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_bookings_user_id (user_id), INDEX idx_bookings_vehicle_id (vehicle_id), INDEX idx_bookings_service_id (service_id), INDEX idx_bookings_status (status), INDEX idx_bookings_created_at (created_at), CONSTRAINT fk_bookings_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_bookings_vehicle_id FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL ON UPDATE CASCADE, CONSTRAINT fk_bookings_service_id FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
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
    addColumn($db, 'products', 'supplier_id', 'supplier_id INT DEFAULT NULL AFTER stock', $executed);
    if (!indexExists($db, 'products', 'idx_products_supplier_id')) {
        execSafe($db, 'ALTER TABLE products ADD INDEX idx_products_supplier_id (supplier_id)', $executed);
    }
    if (tableExists($db, 'suppliers') && !constraintExists($db, 'products', 'fk_products_supplier')) {
        execSafe($db, 'ALTER TABLE products ADD CONSTRAINT fk_products_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE', $executed);
    }
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

// Additional missing tables and columns from audit
if (!tableExists($db, 'api_tokens')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS api_tokens (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, name VARCHAR(191) NOT NULL DEFAULT 'Mobile App', token VARCHAR(128) NOT NULL, revoked TINYINT(1) NOT NULL DEFAULT 0, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, expires_at DATETIME NOT NULL, last_used_at DATETIME DEFAULT NULL, INDEX idx_api_tokens_user_id (user_id), INDEX idx_api_tokens_token (token), INDEX idx_api_tokens_revoked (revoked), CONSTRAINT fk_api_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}
if (tableExists($db, 'api_tokens')) {
    addColumn($db, 'api_tokens', 'name', "name VARCHAR(191) NOT NULL DEFAULT 'Mobile App' AFTER user_id", $executed);
    addColumn($db, 'api_tokens', 'revoked', 'revoked TINYINT(1) NOT NULL DEFAULT 0 AFTER expires_at', $executed);
    addColumn($db, 'api_tokens', 'last_used_at', 'last_used_at DATETIME DEFAULT NULL AFTER expires_at', $executed);
}

if (!tableExists($db, 'api_devices')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS api_devices (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, token_id INT NOT NULL, device_name VARCHAR(191) DEFAULT 'Unknown Device', platform VARCHAR(100) DEFAULT 'unknown', last_active DATETIME DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_api_devices_user_id (user_id), INDEX idx_api_devices_token_id (token_id), CONSTRAINT fk_api_devices_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_api_devices_token FOREIGN KEY (token_id) REFERENCES api_tokens(id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}
if (!tableExists($db, 'api_logs')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS api_logs (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT NULL, endpoint VARCHAR(255) NOT NULL, method VARCHAR(20) NOT NULL, ip_address VARCHAR(50) NOT NULL, response_code INT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_api_logs_user_id (user_id), INDEX idx_api_logs_endpoint (endpoint), CONSTRAINT fk_api_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'chat_messages')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS chat_messages (id INT AUTO_INCREMENT PRIMARY KEY, repair_id INT DEFAULT NULL, sender_id INT DEFAULT NULL, sender_type VARCHAR(50) DEFAULT 'user', message TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_chat_messages_repair_id (repair_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'notifications')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS notifications (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, type VARCHAR(100) DEFAULT 'general', title VARCHAR(191) NOT NULL, message TEXT, status VARCHAR(50) DEFAULT 'sent', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, read_at DATETIME DEFAULT NULL, INDEX idx_notifications_user_id (user_id), INDEX idx_notifications_created_at (created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'sms_logs')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS sms_logs (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, message TEXT, provider VARCHAR(100) DEFAULT 'mock', status VARCHAR(50) DEFAULT 'queued', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_sms_logs_user_id (user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'repair_updates')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS repair_updates (id INT AUTO_INCREMENT PRIMARY KEY, repair_id INT NOT NULL, status VARCHAR(50) DEFAULT NULL, title VARCHAR(191) DEFAULT NULL, description TEXT, created_by INT DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_repair_updates_repair_id (repair_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'repair_notes')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS repair_notes (id INT AUTO_INCREMENT PRIMARY KEY, repair_id INT NOT NULL, user_id INT DEFAULT NULL, note TEXT NOT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_repair_notes_repair_id (repair_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'workshop_tasks')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS workshop_tasks (id INT AUTO_INCREMENT PRIMARY KEY, repair_id INT NOT NULL, technician_id INT DEFAULT NULL, title VARCHAR(191) NOT NULL, description TEXT, priority VARCHAR(20) DEFAULT 'normal', status VARCHAR(50) DEFAULT 'pending', started_at DATETIME DEFAULT NULL, completed_at DATETIME DEFAULT NULL, INDEX idx_workshop_tasks_repair_id (repair_id), INDEX idx_workshop_tasks_technician_id (technician_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'technician_activity')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS technician_activity (id INT AUTO_INCREMENT PRIMARY KEY, technician_id INT NOT NULL, action VARCHAR(191) NOT NULL, description TEXT, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_technician_activity_technician_id (technician_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'maintenance_records')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS maintenance_records (id INT AUTO_INCREMENT PRIMARY KEY, vehicle_id INT NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, service_date DATE NOT NULL, next_service_date DATE DEFAULT NULL, mileage INT DEFAULT 0, status VARCHAR(50) DEFAULT 'scheduled', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_maintenance_records_vehicle_id (vehicle_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'diagnostic_reports')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS diagnostic_reports (id INT AUTO_INCREMENT PRIMARY KEY, vehicle_id INT NOT NULL, analysis TEXT NOT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_diagnostic_reports_vehicle_id (vehicle_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'vehicle_profiles')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS vehicle_profiles (id INT AUTO_INCREMENT PRIMARY KEY, vehicle_id INT NOT NULL, notes TEXT DEFAULT NULL, preferred_service_center VARCHAR(255) DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, UNIQUE KEY uq_vehicle_profile (vehicle_id), INDEX idx_vehicle_profiles_vehicle_id (vehicle_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'obd_error_codes')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS obd_error_codes (id INT AUTO_INCREMENT PRIMARY KEY, code VARCHAR(20) NOT NULL UNIQUE, system VARCHAR(100) DEFAULT NULL, title_fa VARCHAR(255) NOT NULL, description_fa TEXT DEFAULT NULL, severity VARCHAR(20) DEFAULT 'unknown', possible_causes TEXT DEFAULT NULL, recommended_actions TEXT DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_obd_error_codes_code (code)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'diagnostic_sessions')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS diagnostic_sessions (id INT AUTO_INCREMENT PRIMARY KEY, vehicle_id INT NOT NULL, user_id INT NOT NULL, device_type VARCHAR(50) DEFAULT 'ELM327', connection_status VARCHAR(50) DEFAULT 'pending', created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_diagnostic_sessions_vehicle_id (vehicle_id), INDEX idx_diagnostic_sessions_user_id (user_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'diagnostic_results')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS diagnostic_results (id INT AUTO_INCREMENT PRIMARY KEY, session_id INT NOT NULL, error_code_id INT NOT NULL, raw_data TEXT DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_diagnostic_results_session_id (session_id), INDEX idx_diagnostic_results_error_code_id (error_code_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'diagnostic_knowledge')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS diagnostic_knowledge (id INT AUTO_INCREMENT PRIMARY KEY, dtc_code VARCHAR(64) NOT NULL, title VARCHAR(255) NOT NULL, description TEXT DEFAULT NULL, severity VARCHAR(50) DEFAULT NULL, possible_causes TEXT DEFAULT NULL, recommended_actions TEXT DEFAULT NULL, created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, INDEX idx_diagnostic_knowledge_dtc_code (dtc_code)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'repair_recommendations')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS repair_recommendations (id INT AUTO_INCREMENT PRIMARY KEY, diagnostic_id INT NOT NULL, part_name VARCHAR(191) NOT NULL, action VARCHAR(255) NOT NULL, priority INT DEFAULT 0, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_repair_recommendations_diagnostic_id (diagnostic_id)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'orders')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS orders (id INT AUTO_INCREMENT PRIMARY KEY, user_id INT NOT NULL, vehicle_id INT DEFAULT NULL, total_amount DECIMAL(12,2) DEFAULT 0.00, status VARCHAR(30) NOT NULL DEFAULT 'pending', payment_status VARCHAR(30) NOT NULL DEFAULT 'pending', address TEXT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_orders_user_id (user_id), INDEX idx_orders_status (status), INDEX idx_orders_created_at (created_at), CONSTRAINT fk_orders_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_orders_vehicle FOREIGN KEY (vehicle_id) REFERENCES vehicles(id) ON DELETE SET NULL ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
} else {
    addColumn($db, 'orders', 'vehicle_id', 'vehicle_id INT DEFAULT NULL AFTER user_id', $executed);
    addColumn($db, 'orders', 'total_amount', 'total_amount DECIMAL(12,2) DEFAULT 0.00 AFTER vehicle_id', $executed);
    addColumn($db, 'orders', 'payment_status', "payment_status VARCHAR(30) NOT NULL DEFAULT 'pending' AFTER status", $executed);
    addColumn($db, 'orders', 'address', 'address TEXT DEFAULT NULL AFTER payment_status', $executed);
    addColumn($db, 'orders', 'updated_at', 'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at', $executed);
}

if (!tableExists($db, 'order_items')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS order_items (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, product_id INT NOT NULL, quantity INT NOT NULL DEFAULT 1, price DECIMAL(12,2) DEFAULT 0.00, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_order_items_order_id (order_id), INDEX idx_order_items_product_id (product_id), CONSTRAINT fk_order_items_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_order_items_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'payments')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS payments (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, user_id INT NOT NULL, amount DECIMAL(12,2) DEFAULT 0.00, transaction_id VARCHAR(191) DEFAULT NULL, status VARCHAR(50) DEFAULT 'pending', gateway VARCHAR(100) DEFAULT NULL, created_at DATETIME DEFAULT CURRENT_TIMESTAMP, updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_payments_order_id (order_id), INDEX idx_payments_user_id (user_id), CONSTRAINT fk_payments_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'invoices')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS invoices (id INT AUTO_INCREMENT PRIMARY KEY, order_id INT NOT NULL, invoice_number VARCHAR(100) NOT NULL, amount DECIMAL(12,2) DEFAULT 0.00, status VARCHAR(50) DEFAULT 'issued', created_at DATETIME DEFAULT CURRENT_TIMESTAMP, INDEX idx_invoices_order_id (order_id), CONSTRAINT fk_invoices_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'inventory_history')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS inventory_history (id INT AUTO_INCREMENT PRIMARY KEY, product_id INT NOT NULL, admin_id INT DEFAULT NULL, old_stock INT DEFAULT 0, new_stock INT DEFAULT 0, change_amount INT DEFAULT 0, type VARCHAR(30) NOT NULL, note TEXT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, INDEX idx_inventory_history_product_id (product_id), INDEX idx_inventory_history_admin_id (admin_id), INDEX idx_inventory_history_created_at (created_at), CONSTRAINT fk_inventory_history_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_inventory_history_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'suppliers')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS suppliers (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(191) NOT NULL, contact VARCHAR(191) DEFAULT NULL, phone VARCHAR(50) DEFAULT NULL, email VARCHAR(191) DEFAULT NULL, notes TEXT DEFAULT NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, KEY idx_suppliers_name (name), KEY idx_suppliers_created_at (created_at)) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (!tableExists($db, 'product_compatibility')) {
    execSafe($db, "CREATE TABLE IF NOT EXISTS product_compatibility (id INT AUTO_INCREMENT PRIMARY KEY, product_id INT NOT NULL, model_id INT NOT NULL, year INT DEFAULT NULL, engine_type VARCHAR(100) DEFAULT NULL, UNIQUE KEY uq_product_compatibility (product_id, model_id, year, engine_type), INDEX idx_product_compatibility_product_id (product_id), INDEX idx_product_compatibility_model_id (model_id), CONSTRAINT fk_product_compatibility_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE, CONSTRAINT fk_product_compatibility_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci", $executed);
}

if (tableExists($db, 'technicians')) {
    addColumn($db, 'technicians', 'specialization', 'specialization VARCHAR(191) DEFAULT NULL AFTER phone', $executed);
}

if (!tableExists($db, 'notifications')) {
    // already created above, just in case the earlier statement failed
}

if (!tableExists($db, 'vehicles') && !tableExists($db, 'vehicle_brands')) {
    // no action; vehicles likely exist already.
}

// Harmonize vehicle_models
if (tableExists($db, 'vehicle_models')) {
    addColumn($db, 'vehicle_models', 'slug', 'slug VARCHAR(191) DEFAULT NULL AFTER name_fa', $executed);
    addColumn($db, 'vehicle_models', 'year_from', 'year_from INT DEFAULT NULL AFTER slug', $executed);
    addColumn($db, 'vehicle_models', 'year_to', 'year_to INT DEFAULT NULL AFTER year_from', $executed);
}

// Ensure orders columns exist
if (tableExists($db, 'orders')) {
    addColumn($db, 'orders', 'vehicle_id', 'vehicle_id INT DEFAULT NULL AFTER user_id', $executed);
    addColumn($db, 'orders', 'total_amount', 'total_amount DECIMAL(12,2) DEFAULT 0.00 AFTER vehicle_id', $executed);
    addColumn($db, 'orders', 'payment_status', "payment_status VARCHAR(30) NOT NULL DEFAULT 'pending' AFTER status", $executed);
    addColumn($db, 'orders', 'address', 'address TEXT DEFAULT NULL AFTER payment_status', $executed);
    addColumn($db, 'orders', 'updated_at', 'updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at', $executed);
}

if (!tableExists($db, 'slides')) {
    // no slide resolution needed
}

// Show results
if ($executed) {
    echo "Schema stabilization actions:\n";
    foreach ($executed as $item) {
        echo $item . "\n";
    }
} else {
    echo "No schema changes were required.\n";
}

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../app/Core/Database.php';

$db = App\Core\Database::connect();
$sql = file_get_contents(__DIR__ . '/../database/inventory_migration.sql');
if ($sql === false) {
    echo "Migration file not found.\n";
    exit(1);
}

$parts = preg_split('/;\s*\r?\n/', $sql);
foreach ($parts as $part) {
    $part = trim($part);
    if ($part === '') {
        continue;
    }

    if (stripos($part, 'ALTER TABLE products') === 0 && stripos($part, 'ADD COLUMN IF NOT EXISTS supplier_id') !== false) {
        $check = $db->query("SHOW COLUMNS FROM products LIKE 'supplier_id'");
        if ($check && $check->fetch(PDO::FETCH_ASSOC)) {
            echo "Column products.supplier_id already exists, skipping ADD COLUMN.\n";
        } else {
            echo "Adding products.supplier_id column...\n";
            $addColumn = 'ALTER TABLE products ADD COLUMN supplier_id INT DEFAULT NULL AFTER stock';
            $db->exec($addColumn);
            echo "  OK: added supplier_id column.\n";
        }

        $checkIndex = $db->query("SHOW KEYS FROM products WHERE Key_name = 'idx_products_supplier_id'");
        if (!($checkIndex && $checkIndex->fetch(PDO::FETCH_ASSOC))) {
            echo "Adding idx_products_supplier_id index...\n";
            $db->exec('ALTER TABLE products ADD KEY idx_products_supplier_id (supplier_id)');
            echo "  OK: added supplier index.\n";
        } else {
            echo "Index idx_products_supplier_id already exists, skipping.\n";
        }

        $fkCheck = $db->query("SELECT CONSTRAINT_NAME FROM information_schema.KEY_COLUMN_USAGE WHERE TABLE_SCHEMA = " . $db->quote(DB_NAME) . " AND TABLE_NAME = 'products' AND COLUMN_NAME = 'supplier_id' AND REFERENCED_TABLE_NAME = 'suppliers'");
        if (!($fkCheck && $fkCheck->fetch(PDO::FETCH_ASSOC))) {
            echo "Adding foreign key fk_products_supplier...\n";
            $db->exec('ALTER TABLE products ADD CONSTRAINT fk_products_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE');
            echo "  OK: added foreign key.\n";
        } else {
            echo "Foreign key fk_products_supplier already exists, skipping.\n";
        }
        continue;
    }

    try {
        $db->exec($part);
        echo "EXECUTED: " . substr($part, 0, 80) . "\n";
    } catch (PDOException $e) {
        echo "SKIP/ERROR: " . $e->getMessage() . "\n";
    }
}

echo "Migration done.\n";

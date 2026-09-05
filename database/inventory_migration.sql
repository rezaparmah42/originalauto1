CREATE TABLE IF NOT EXISTS inventory_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    old_stock INT DEFAULT 0,
    new_stock INT DEFAULT 0,
    change_amount INT DEFAULT 0,
    type VARCHAR(30) NOT NULL,
    note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_inventory_history_product_id (product_id),
    KEY idx_inventory_history_admin_id (admin_id),
    KEY idx_inventory_history_created_at (created_at),
    CONSTRAINT fk_inventory_history_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inventory_history_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS suppliers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(191) NOT NULL,
    contact VARCHAR(191) DEFAULT NULL,
    phone VARCHAR(50) DEFAULT NULL,
    email VARCHAR(191) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_suppliers_name (name),
    KEY idx_suppliers_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE products
    ADD COLUMN IF NOT EXISTS supplier_id INT DEFAULT NULL AFTER stock,
    ADD KEY idx_products_supplier_id (supplier_id),
    ADD CONSTRAINT fk_products_supplier FOREIGN KEY (supplier_id) REFERENCES suppliers(id) ON DELETE SET NULL ON UPDATE CASCADE;

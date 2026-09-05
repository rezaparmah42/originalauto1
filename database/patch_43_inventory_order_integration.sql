CREATE TABLE IF NOT EXISTS inventory_transactions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT DEFAULT NULL,
    product_id INT NOT NULL,
    admin_id INT DEFAULT NULL,
    old_stock INT DEFAULT 0,
    new_stock INT DEFAULT 0,
    change_amount INT NOT NULL DEFAULT 0,
    type VARCHAR(40) NOT NULL DEFAULT 'order_deduction',
    note TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_inventory_transactions_order_id (order_id),
    KEY idx_inventory_transactions_product_id (product_id),
    KEY idx_inventory_transactions_created_at (created_at),
    UNIQUE KEY uq_inventory_transactions_order_product (order_id, product_id),
    CONSTRAINT fk_inventory_transactions_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_inventory_transactions_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_inventory_transactions_admin FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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

ALTER TABLE inventory_history
    ADD COLUMN IF NOT EXISTS order_id INT DEFAULT NULL AFTER product_id,
    ADD KEY idx_inventory_history_order_id (order_id),
    ADD CONSTRAINT fk_inventory_history_order FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE SET NULL ON UPDATE CASCADE;

ALTER TABLE order_items
    ADD COLUMN IF NOT EXISTS price_snapshot DECIMAL(12,2) DEFAULT 0.00 AFTER quantity;

ALTER TABLE orders
    ADD COLUMN IF NOT EXISTS payment_status VARCHAR(30) NOT NULL DEFAULT 'pending' AFTER status;

CREATE INDEX IF NOT EXISTS idx_orders_payment_status ON orders(payment_status);

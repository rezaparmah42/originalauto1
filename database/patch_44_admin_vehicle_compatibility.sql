CREATE TABLE IF NOT EXISTS product_compatibility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    model_id INT NOT NULL,
    year INT DEFAULT NULL,
    engine_type VARCHAR(100) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_compatibility (product_id, model_id, year, engine_type),
    KEY idx_product_compatibility_product_id (product_id),
    KEY idx_product_compatibility_model_id (model_id),
    CONSTRAINT fk_product_compatibility_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_product_compatibility_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE product_compatibility
    ADD COLUMN IF NOT EXISTS created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER engine_type,
    ADD UNIQUE KEY uq_product_compatibility (product_id, model_id, year, engine_type),
    ADD KEY idx_product_compatibility_product_id (product_id),
    ADD KEY idx_product_compatibility_model_id (model_id);

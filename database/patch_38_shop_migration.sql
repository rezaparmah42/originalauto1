-- PATCH 38: Add product categories and product images tables, and add columns to products

ALTER TABLE products
  ADD COLUMN IF NOT EXISTS category_id INT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS featured TINYINT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS views_count INT DEFAULT 0,
  ADD COLUMN IF NOT EXISTS seo_title_fa VARCHAR(200) DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS seo_description_fa TEXT DEFAULT NULL,
  ADD COLUMN IF NOT EXISTS search_keywords_fa TEXT DEFAULT NULL;

ALTER TABLE products
  ADD INDEX IF NOT EXISTS idx_products_category_id (category_id),
  ADD INDEX IF NOT EXISTS idx_products_featured (featured),
  ADD INDEX IF NOT EXISTS idx_products_status (status),
  ADD INDEX IF NOT EXISTS idx_products_slug (slug);

CREATE TABLE IF NOT EXISTS product_categories (
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

CREATE TABLE IF NOT EXISTS product_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  product_id INT NOT NULL,
  image VARCHAR(255) NOT NULL,
  alt_text VARCHAR(255) DEFAULT NULL,
  display_order INT DEFAULT 0,
  is_primary TINYINT DEFAULT 0,
  status TINYINT DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  KEY idx_product_images_product_id (product_id),
  KEY idx_product_images_primary (is_primary),
  CONSTRAINT fk_product_images_product FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

ALTER TABLE product_images
  ADD COLUMN IF NOT EXISTS is_primary TINYINT DEFAULT 0;

ALTER TABLE orders
  ADD COLUMN IF NOT EXISTS total_amount DECIMAL(12,2) DEFAULT 0.00,
  ADD COLUMN IF NOT EXISTS payment_status VARCHAR(30) NOT NULL DEFAULT 'unpaid',
  ADD COLUMN IF NOT EXISTS address TEXT DEFAULT NULL;

ALTER TABLE order_items
  ADD COLUMN IF NOT EXISTS price_snapshot DECIMAL(12,2) DEFAULT 0.00;

ALTER TABLE orders
  ADD INDEX IF NOT EXISTS idx_orders_payment_status (payment_status),
  ADD INDEX IF NOT EXISTS idx_orders_status (status);


SET FOREIGN_KEY_CHECKS = 0;

DROP DATABASE IF EXISTS original_east;
CREATE DATABASE IF NOT EXISTS original_east
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE original_east;

DROP TABLE IF EXISTS media_relations;
DROP TABLE IF EXISTS media_library;
DROP TABLE IF EXISTS product_vehicle_model;
DROP TABLE IF EXISTS article_vehicle_model;
DROP TABLE IF EXISTS vehicle_model_service;
DROP TABLE IF EXISTS vehicle_diagnostic_tools;
DROP TABLE IF EXISTS vehicle_dtc_codes;
DROP TABLE IF EXISTS vehicle_ecu_info;
DROP TABLE IF EXISTS vehicle_diagnostics;
DROP TABLE IF EXISTS vehicle_maintenance_tasks;
DROP TABLE IF EXISTS vehicle_repair_solutions;
DROP TABLE IF EXISTS vehicle_common_problems;
DROP TABLE IF EXISTS vehicle_model_faqs;
DROP TABLE IF EXISTS vehicle_symptoms;
DROP TABLE IF EXISTS vehicle_models;
DROP TABLE IF EXISTS vehicle_brands;
DROP TABLE IF EXISTS reviews;
DROP TABLE IF EXISTS orders;
DROP TABLE IF EXISTS repairs;
DROP TABLE IF EXISTS bookings;
DROP TABLE IF EXISTS products;
DROP TABLE IF EXISTS articles;
DROP TABLE IF EXISTS services;
DROP TABLE IF EXISTS vehicles;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(30),
    email VARCHAR(150),
    password VARCHAR(255),
    role VARCHAR(30) DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_users_role (role),
    INDEX idx_users_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name_fa VARCHAR(150) NOT NULL,
    name_en VARCHAR(150) DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    category VARCHAR(80) NOT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    priority_order SMALLINT UNSIGNED DEFAULT 100,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_vehicle_brands_slug (slug),
    INDEX idx_vehicle_brands_category (category),
    INDEX idx_vehicle_brands_status (status),
    INDEX idx_vehicle_brands_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_models (
    id INT AUTO_INCREMENT PRIMARY KEY,
    brand_id INT NOT NULL,
    name_fa VARCHAR(180) NOT NULL,
    name_en VARCHAR(180) DEFAULT NULL,
    common_name_fa VARCHAR(180) DEFAULT NULL,
    common_name_en VARCHAR(180) DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    year_from SMALLINT UNSIGNED DEFAULT NULL,
    year_to SMALLINT UNSIGNED DEFAULT NULL,
    engine_type VARCHAR(120) DEFAULT NULL,
    fuel_type VARCHAR(80) DEFAULT NULL,
    drivetrain VARCHAR(80) DEFAULT NULL,
    body_type VARCHAR(80) DEFAULT NULL,
    maintenance_overview_fa TEXT DEFAULT NULL,
    maintenance_overview_en TEXT DEFAULT NULL,
    diagnostic_overview_fa TEXT DEFAULT NULL,
    diagnostic_overview_en TEXT DEFAULT NULL,
    expert_notes_fa TEXT DEFAULT NULL,
    expert_notes_en TEXT DEFAULT NULL,
    seo_title_fa VARCHAR(200) DEFAULT NULL,
    seo_title_en VARCHAR(200) DEFAULT NULL,
    seo_description_fa TEXT DEFAULT NULL,
    seo_description_en TEXT DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    expert_level TINYINT DEFAULT 1,
    verified_by_mechanic TINYINT DEFAULT 0,
    views_count INT UNSIGNED DEFAULT 0,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_vehicle_models_slug (slug),
    KEY idx_vehicle_models_brand_id (brand_id),
    KEY idx_vehicle_models_status (status),
    KEY idx_vehicle_models_created_at (created_at),
    FULLTEXT KEY ft_vehicle_models_content (maintenance_overview_fa, maintenance_overview_en, diagnostic_overview_fa, diagnostic_overview_en, expert_notes_fa, expert_notes_en, search_keywords_fa, search_keywords_en),
    CONSTRAINT fk_vehicle_models_brand_id FOREIGN KEY (brand_id) REFERENCES vehicle_brands(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_models_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_models_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_model_faqs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    question_fa VARCHAR(255) NOT NULL,
    question_en VARCHAR(255) DEFAULT NULL,
    answer_fa TEXT NOT NULL,
    answer_en TEXT DEFAULT NULL,
    display_order SMALLINT UNSIGNED DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_vehicle_model_faqs_model_id (model_id),
    INDEX idx_vehicle_model_faqs_status (status),
    CONSTRAINT fk_vehicle_model_faqs_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_symptoms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    title_fa VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    detail_fa TEXT DEFAULT NULL,
    detail_en TEXT DEFAULT NULL,
    category VARCHAR(80) DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    expert_level TINYINT DEFAULT 1,
    verified_by_mechanic TINYINT DEFAULT 0,
    views_count INT UNSIGNED DEFAULT 0,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_vehicle_symptoms_slug (slug),
    KEY idx_vehicle_symptoms_model_id (model_id),
    KEY idx_vehicle_symptoms_category (category),
    KEY idx_vehicle_symptoms_status (status),
    KEY idx_vehicle_symptoms_created_at (created_at),
    FULLTEXT KEY ft_vehicle_symptoms_content (title_fa, detail_fa, search_keywords_fa),
    CONSTRAINT fk_vehicle_symptoms_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_symptoms_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_symptoms_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_common_problems (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    title_fa VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    symptoms_fa TEXT DEFAULT NULL,
    symptoms_en TEXT DEFAULT NULL,
    cause_fa TEXT DEFAULT NULL,
    cause_en TEXT DEFAULT NULL,
    severity VARCHAR(80) DEFAULT NULL,
    category VARCHAR(80) DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    expert_level TINYINT DEFAULT 1,
    verified_by_mechanic TINYINT DEFAULT 0,
    views_count INT UNSIGNED DEFAULT 0,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_vehicle_common_problems_slug (slug),
    KEY idx_vehicle_common_problems_model_id (model_id),
    KEY idx_vehicle_common_problems_category (category),
    KEY idx_vehicle_common_problems_status (status),
    KEY idx_vehicle_common_problems_created_at (created_at),
    FULLTEXT KEY ft_vehicle_common_problems_content (title_fa, symptoms_fa, cause_fa, search_keywords_fa),
    CONSTRAINT fk_vehicle_common_problems_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_common_problems_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_common_problems_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_repair_solutions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    problem_id INT NOT NULL,
    title_fa VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) DEFAULT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    required_parts_fa TEXT DEFAULT NULL,
    required_parts_en TEXT DEFAULT NULL,
    estimated_time VARCHAR(120) DEFAULT NULL,
    service_id INT DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    expert_level TINYINT DEFAULT 1,
    verified_by_mechanic TINYINT DEFAULT 0,
    views_count INT UNSIGNED DEFAULT 0,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_vehicle_repair_solutions_problem_id (problem_id),
    KEY idx_vehicle_repair_solutions_service_id (service_id),
    KEY idx_vehicle_repair_solutions_status (status),
    KEY idx_vehicle_repair_solutions_created_at (created_at),
    FULLTEXT KEY ft_vehicle_repair_solutions_content (title_fa, description_fa, required_parts_fa, search_keywords_fa),
    CONSTRAINT fk_vehicle_repair_solutions_problem_id FOREIGN KEY (problem_id) REFERENCES vehicle_common_problems(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_repair_solutions_service_id FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_repair_solutions_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_repair_solutions_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_maintenance_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    title_fa VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) DEFAULT NULL,
    interval_km INT UNSIGNED DEFAULT NULL,
    interval_months INT UNSIGNED DEFAULT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    required_parts_fa TEXT DEFAULT NULL,
    required_parts_en TEXT DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    expert_level TINYINT DEFAULT 1,
    verified_by_mechanic TINYINT DEFAULT 0,
    views_count INT UNSIGNED DEFAULT 0,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_vehicle_maintenance_tasks_model_id (model_id),
    KEY idx_vehicle_maintenance_tasks_status (status),
    KEY idx_vehicle_maintenance_tasks_created_at (created_at),
    FULLTEXT KEY ft_vehicle_maintenance_tasks_content (title_fa, description_fa, required_parts_fa, search_keywords_fa),
    CONSTRAINT fk_vehicle_maintenance_tasks_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_maintenance_tasks_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_maintenance_tasks_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_diagnostics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    code VARCHAR(80) DEFAULT NULL,
    title_fa VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) DEFAULT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    test_steps_fa TEXT DEFAULT NULL,
    test_steps_en TEXT DEFAULT NULL,
    recommended_tools_fa TEXT DEFAULT NULL,
    recommended_tools_en TEXT DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    expert_level TINYINT DEFAULT 1,
    verified_by_mechanic TINYINT DEFAULT 0,
    views_count INT UNSIGNED DEFAULT 0,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_vehicle_diagnostics_model_id (model_id),
    KEY idx_vehicle_diagnostics_code (code),
    KEY idx_vehicle_diagnostics_status (status),
    KEY idx_vehicle_diagnostics_created_at (created_at),
    FULLTEXT KEY ft_vehicle_diagnostics_content (title_fa, description_fa, test_steps_fa, recommended_tools_fa, search_keywords_fa),
    CONSTRAINT fk_vehicle_diagnostics_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_diagnostics_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_diagnostics_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_ecu_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    ecu_name VARCHAR(160) NOT NULL,
    ecu_type VARCHAR(80) DEFAULT NULL,
    ecu_vendor VARCHAR(120) DEFAULT NULL,
    protocol VARCHAR(120) DEFAULT NULL,
    software_version VARCHAR(120) DEFAULT NULL,
    notes_fa TEXT DEFAULT NULL,
    notes_en TEXT DEFAULT NULL,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_vehicle_ecu_info_model_id (model_id),
    KEY idx_vehicle_ecu_info_status (status),
    CONSTRAINT fk_vehicle_ecu_info_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_ecu_info_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_ecu_info_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_dtc_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    code VARCHAR(80) NOT NULL,
    title_fa VARCHAR(255) NOT NULL,
    title_en VARCHAR(255) DEFAULT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    probable_cause_fa TEXT DEFAULT NULL,
    probable_cause_en TEXT DEFAULT NULL,
    repair_advice_fa TEXT DEFAULT NULL,
    repair_advice_en TEXT DEFAULT NULL,
    severity VARCHAR(80) DEFAULT NULL,
    revision_number INT UNSIGNED DEFAULT 1,
    created_by INT DEFAULT NULL,
    updated_by INT DEFAULT NULL,
    last_updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_vehicle_dtc_codes_model_code (model_id, code),
    KEY idx_vehicle_dtc_codes_model_id (model_id),
    KEY idx_vehicle_dtc_codes_status (status),
    CONSTRAINT fk_vehicle_dtc_codes_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_dtc_codes_created_by FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_dtc_codes_updated_by FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_diagnostic_tools (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(180) NOT NULL,
    compatible_with VARCHAR(160) DEFAULT NULL,
    supported_protocols VARCHAR(200) DEFAULT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_vehicle_diagnostic_tools_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media_library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_type VARCHAR(80) DEFAULT NULL,
    alt_text_fa VARCHAR(255) DEFAULT NULL,
    alt_text_en VARCHAR(255) DEFAULT NULL,
    tags VARCHAR(255) DEFAULT NULL,
    uploaded_by INT DEFAULT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_media_library_file_type (file_type),
    INDEX idx_media_library_uploaded_at (uploaded_at),
    CONSTRAINT fk_media_library_uploaded_by FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE media_relations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    media_id INT NOT NULL,
    entity_type VARCHAR(80) NOT NULL,
    entity_id INT NOT NULL,
    context VARCHAR(80) DEFAULT NULL,
    display_order SMALLINT UNSIGNED DEFAULT 0,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_media_relations_media_id (media_id),
    KEY idx_media_relations_entity (entity_type, entity_id),
    CONSTRAINT fk_media_relations_media_id FOREIGN KEY (media_id) REFERENCES media_library(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_fa VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    seo_title_fa VARCHAR(200) DEFAULT NULL,
    seo_title_en VARCHAR(200) DEFAULT NULL,
    seo_description_fa TEXT DEFAULT NULL,
    seo_description_en TEXT DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    price VARCHAR(60) DEFAULT NULL,
    duration VARCHAR(80) DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_services_slug (slug),
    KEY idx_services_status (status),
    KEY idx_services_created_at (created_at),
    FULLTEXT KEY ft_services_content (title_fa, description_fa, search_keywords_fa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE articles (
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

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title_fa VARCHAR(200) NOT NULL,
    title_en VARCHAR(200) DEFAULT NULL,
    slug VARCHAR(191) NOT NULL,
    description_fa TEXT DEFAULT NULL,
    description_en TEXT DEFAULT NULL,
    seo_title_fa VARCHAR(200) DEFAULT NULL,
    seo_title_en VARCHAR(200) DEFAULT NULL,
    seo_description_fa TEXT DEFAULT NULL,
    seo_description_en TEXT DEFAULT NULL,
    search_keywords_fa TEXT DEFAULT NULL,
    search_keywords_en TEXT DEFAULT NULL,
    price DECIMAL(12,2) DEFAULT 0,
    stock INT DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_products_slug (slug),
    KEY idx_products_status (status),
    KEY idx_products_created_at (created_at),
    FULLTEXT KEY ft_products_content (title_fa, description_fa, search_keywords_fa)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicle_model_service (
    id INT AUTO_INCREMENT PRIMARY KEY,
    model_id INT NOT NULL,
    service_id INT NOT NULL,
    UNIQUE KEY uq_vehicle_model_service (model_id, service_id),
    KEY idx_vehicle_model_service_model_id (model_id),
    KEY idx_vehicle_model_service_service_id (service_id),
    CONSTRAINT fk_vehicle_model_service_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_vehicle_model_service_service_id FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE article_vehicle_model (
    id INT AUTO_INCREMENT PRIMARY KEY,
    article_id INT NOT NULL,
    model_id INT NOT NULL,
    UNIQUE KEY uq_article_vehicle_model (article_id, model_id),
    KEY idx_article_vehicle_model_article_id (article_id),
    KEY idx_article_vehicle_model_model_id (model_id),
    CONSTRAINT fk_article_vehicle_model_article_id FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_article_vehicle_model_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE product_vehicle_model (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    model_id INT NOT NULL,
    UNIQUE KEY uq_product_vehicle_model (product_id, model_id),
    KEY idx_product_vehicle_model_product_id (product_id),
    KEY idx_product_vehicle_model_model_id (model_id),
    CONSTRAINT fk_product_vehicle_model_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_product_vehicle_model_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    brand VARCHAR(100),
    model VARCHAR(100),
    year VARCHAR(20),
    engine VARCHAR(100),
    vin VARCHAR(100),
    mileage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_vehicles_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vehicle_id INT,
    service_id INT,
    problem TEXT,
    status VARCHAR(50) DEFAULT 'new',
    booking_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_bookings_user_id (user_id),
    INDEX idx_bookings_vehicle_id (vehicle_id),
    INDEX idx_bookings_service_id (service_id),
    INDEX idx_bookings_status (status),
    INDEX idx_bookings_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE repairs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    diagnosis TEXT,
    repair_notes TEXT,
    cost DECIMAL(12,2) DEFAULT 0,
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_repairs_booking_id (booking_id),
    INDEX idx_repairs_status (status),
    INDEX idx_repairs_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(12,2),
    status VARCHAR(50) DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_orders_user_id (user_id),
    INDEX idx_orders_status (status),
    INDEX idx_orders_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    service_id INT,
    rating INT,
    comment TEXT,
    status VARCHAR(30) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reviews_user_id (user_id),
    INDEX idx_reviews_service_id (service_id),
    INDEX idx_reviews_status (status),
    INDEX idx_reviews_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (name, phone, email, password, role) VALUES
('مدیر سایت', '09120000000', 'admin@originaleast.ir', 'password', 'admin'),
('کاربر تست', '09121111111', 'customer@originaleast.ir', 'password', 'customer');

INSERT INTO vehicle_brands (name_fa, name_en, slug, category, description_fa, description_en, priority_order, status) VALUES
('پژو', 'Peugeot', 'peugeot', 'داخلی', 'برند پژو یکی از محبوب‌ترین خودروهای سواری در بازار ایران است.', 'Peugeot is one of the most popular passenger car brands in Iran.', 1, 1),
('سایپا', 'Saipa', 'saipa', 'داخلی', 'سایپا با محصولات کوچک و اقتصادی شناخته می‌شود.', 'Saipa is known for compact and economical cars.', 2, 1),
('ایران خودرو', 'Iran Khodro', 'iran-khodro', 'داخلی', 'ایران خودرو تولیدکننده خودروهای ملی و پرطرفدار بازار است.', 'Iran Khodro is the manufacturer of national and popular cars.', 3, 1),
('MVM', 'MVM', 'mvm', 'چینی', 'MVM یکی از برندهای چینی شناخته شده در بازار ایران است.', 'MVM is a recognized Chinese brand in Iran.', 4, 1),
('چری', 'Chery', 'chery', 'چینی', 'چری در بازار ایران با خودروهای خانواده‌ای و کراس اوور شناخته شده است.', 'Chery is known for family and crossover cars in Iran.', 5, 1),
('تیگو', 'Tiggo', 'tiggo', 'چینی', 'تیگو محصولات کراس اوور چری را در بر می‌گیرد.', 'Tiggo covers Chery crossover products.', 6, 1),
('JAC', 'JAC', 'jac', 'چینی', 'JAC خودروی شاسی‌بلند و تجاری در بازار ایران دارد.', 'JAC offers SUVs and commercial vehicles in Iran.', 7, 1),
('KMC', 'KMC', 'kmc', 'چینی', 'KMC محصولات کراس اوور و تجاری ارائه می‌دهد.', 'KMC offers crossover and commercial models.', 8, 1),
('هیوندای', 'Hyundai', 'hyundai', 'کره‌ای', 'هیوندای یکی از برندهای معتبر کره‌ای با حضور گسترده در ایران است.', 'Hyundai is one of the well-regarded Korean brands in Iran.', 9, 1),
('کیا', 'Kia', 'kia', 'کره‌ای', 'کیا با مدل‌های خانوادگی و اسپرت در بازار ایران شناخته می‌شود.', 'Kia is known for family and sport models in Iran.', 10, 1),
('تویوتا', 'Toyota', 'toyota', 'ژاپنی', 'تویوتا با کیفیت ساخت و خدمات پس از فروش قابل اعتماد در ایران حضور دارد.', 'Toyota has reliable build quality and after-sales service in Iran.', 11, 1);

INSERT INTO vehicle_models (brand_id, name_fa, name_en, common_name_fa, common_name_en, slug, year_from, year_to, engine_type, fuel_type, drivetrain, body_type, maintenance_overview_fa, diagnostic_overview_fa, expert_notes_fa, seo_title_fa, seo_description_fa, search_keywords_fa, expert_level, verified_by_mechanic, created_by) VALUES
(1, 'پژو 206 تیپ 2', 'Peugeot 206 Type 2', '206', '206', 'peugeot-206', 2005, 2015, 'TU3 1.4L', 'بنزین', 'FWD', 'هاچ‌بک', 'سرویس دوره‌ای پژو 206 شامل تعویض روغن، فیلتر هوا و بازدید سیستم ترمز است.', 'فناوری دیاگ پژو 206 نیاز به دانش سنسورها دارد و خطاهای EML باید بررسی شوند.', 'این مدل در کارگاه ما به طور ثابت با مشکلات احتراق و سنسور اکسیژن مواجه است.', 'مشکلات و سرویس پژو 206', 'راهنمای عیب‌یابی و تعمیر پژو 206 برای بازار ایران', 'پژو 206, مشکلات پژو 206, سرویس پژو 206', 3, 1, 1),
(2, 'سایپا پراید 111', 'Saipa Pride 111', 'پراید', 'Pride', 'saipa-pride-111', 2008, 2015, 'SOHC 1.0L', 'بنزین', 'FWD', 'هاچ‌بک', 'پراید 111 سرویس دوره‌ای ساده اما ضروری دارد تا مشکلات برق و کاربراتور کاهش یابد.', 'عمده‌ترین خطاهای این مدل مربوط به سیستم برقی و تنظیم سوخت است.', 'در تعمیرگاه ما، اولویت با بررسی اتصال‌های برق و وضعیت شمع است.', 'راهنمای سرویس پراید 111', 'نکات فنی نگهداری و عیب‌یابی پراید 111', 'پراید, سرویس پراید, مشکلات پراید', 2, 1, 1),
(5, 'چری آریزو 5', 'Chery Arrizo 5', 'آریزو 5', 'Arrizo 5', 'chery-arrizo-5', 2016, 2021, 'ACTECO 1.5L', 'بنزین', 'FWD', 'سدان', 'آریزو 5 نیاز به سرویس به موقع روغن گیربکس و بررسی سیستم برقی دارد.', 'دیاگ این مدل معمولاً خطاهای فشار سوخت و MAP را نشان می‌دهد.', 'تجربه کارگاهی نشان می‌دهد نگهداری سیستم خنک‌کننده برای این خودرو ضروری است.', 'مشکلات فنی چری آریزو 5', 'سرویس و دیاگ تخصصی چری آریزو 5', 'چری آریزو, تعمیر چری آریزو, دیاگ آریزو', 3, 1, 1),
(6, 'تیگو 5', 'Tiggo 5', 'تیگو 5', 'Tiggo 5', 'tiggo-5', 2014, 2020, 'ACTECO 2.0L', 'بنزین', 'FWD', 'کراس‌اوور', 'سرویس تیگو 5 باید روی جلوبندی و روغن گیربکس تمرکز کند.', 'خطاهای گیربکس CVT و سنسور اکسیژن در تیگو متداول است.', 'در کارگاه ما، اغلب خرابی‌های تیگو از سیم‌کشی سنسورها شروع می‌شود.', 'سرویس و عیب‌یابی تیگو 5', 'راهنمای تعمیرات تخصصی تیگو 5', 'تیگو 5, خرابی گیربکس تیگو, مشکلات برقی تیگو', 3, 1, 1),
(9, 'هیوندای النترا', 'Hyundai Elantra', 'النترا', 'Elantra', 'hyundai-elantra', 2012, 2018, 'Gamma 1.6L', 'بنزین', 'FWD', 'سدان', 'النترا باید سرویس موتور و سیستم تعلیق را به صورت منظم دریافت کند.', 'دیاگ النترا معمولاً کدهای سنسور اکسیژن و EGR را نمایش می‌دهد.', 'تعمیر و نگهداری این مدل روی سیستم تزریق سوخت تأثیر مستقیم دارد.', 'سرویس دوره‌ای هیوندای النترا', 'راهنمای عیب‌یابی و نگهداری هیوندای النترا', 'هیوندای النترا, مشکلات هیوندای, سرویس النترا', 3, 1, 1),
(11, 'تویوتا کرولا', 'Toyota Corolla', 'کرولا', 'Corolla', 'toyota-corolla', 2014, 2020, '1ZR-FE 1.6L', 'بنزین', 'FWD', 'سدان', 'کرولا ماشین قابل اعتمادی است اما سرویس دقیق شمع و تسمه تایم برای آن ضروری است.', 'دیاگ تویوتا کرولا معمولاً خطاهای سیستم احتراق و سنسور MAF را نشان می‌دهد.', 'تجربه نشان داده است که نگهداری صحیح سیستم سوخت‌رسانی عمر موتور را افزایش می‌دهد.', 'راهنمای تعمیرات تویوتا کرولا', 'نکات تخصصی تعمیرات و سرویس کرولا', 'تویوتا کرولا, تعمیرات تویوتا, سرویس کرولا', 4, 1, 1);

INSERT INTO services (title_fa, title_en, slug, description_fa, seo_title_fa, seo_description_fa, search_keywords_fa, price, duration, status) VALUES
('دیاگ تخصصی خودرو', 'Vehicle Diagnostic', 'diagnostic-service', 'عیب‌یابی دقیق با دستگاه دیاگ و بررسی کدهای خطا برای خودروهای ایرانی و وارداتی.', 'دیاگ تخصصی اورجینال شرق', 'تشخیص خطاهای خودرو و دیاگ تخصصی برای انواع مدل‌ها.', 'دیاگ, تعمیرات خودرو, کد خطا', '350000', '1 روز', 1),
('سرویس دوره‌ای کامل', 'Full Maintenance Service', 'full-maintenance-service', 'سرویس کامل روغن، فیلتر، ترمز و بررسی عمومی خودرو با استاندارد کارگاهی.', 'سرویس دوره‌ای اورجینال شرق', 'سرویس دوره‌ای کامل برای خودروهای ایرانی و وارداتی.', 'سرویس دوره ای, تعویض روغن, بازدید', '450000', '1 روز', 1),
('تعمیر گیربکس اتوماتیک', 'Automatic Transmission Repair', 'gearbox-repair', 'عیب‌یابی و تعمیر گیربکس اتوماتیک با بررسی شیر برقی، مایع گیربکس و قطعات داخلی.', 'تعمیر گیربکس اورجینال شرق', 'تعمیر تخصصی گیربکس اتوماتیک با تجربه کارگاهی.', 'گیربکس, تعمیر گیربکس, خطای گیربکس', '1200000', '2 روز', 1);

INSERT INTO articles (title_fa, title_en, slug, category, author, content_fa, seo_title_fa, seo_description_fa, search_keywords_fa, status) VALUES
('راهنمای سرویس پژو 206 در تهران', 'Peugeot 206 Maintenance Guide in Tehran', 'peugeot-206-maintenance-guide', 'خودرو', 'مهندس تعمیرگاه', 'این راهنمای عملی سرویس پژو 206 را از نگاه مکانیک‌های کارگاهی توضیح می‌دهد.', 'سرویس پژو 206 در تهران', 'راهنمای عملی سرویس و نگهداری پژو 206 در تهران.', 'سرویس پژو 206, تعمیر پژو 206', 1),
('مشکلات برقی کیا سراتو', 'Kia Cerato Electrical Issues', 'kia-cerato-electrical-issues', 'برقی', 'کارشناس برق', 'در این مقاله مشکلات برقی متداول کیا سراتو و روش‌های تست دقیق بررسی شده است.', 'مشکلات برقی کیا سراتو', 'عیب‌یابی و تعمیر مشکلات برقی کیا سراتو.', 'مشکلات برقی کیا, تعمیرات کیا', 1);

INSERT INTO products (title_fa, title_en, slug, description_fa, seo_title_fa, seo_description_fa, search_keywords_fa, price, stock, status) VALUES
('شمع NGK مناسب پژو 206', 'NGK Spark Plug for Peugeot 206', 'ngk-spark-plug-peugeot-206', 'شمع NGK اصلی با کیفیت مناسب برای موتور TU3 پژو 206.', 'شمع NGK پژو 206', 'شمع NGK اصلی جهت تعمیر و سرویس پژو 206.', 'شمع پژو 206, قطعات پژو 206', 450000, 20, 1),
('فیلتر هوا سایپا پراید', 'Saipa Pride Air Filter', 'saipa-pride-air-filter', 'فیلتر هوای استاندارد برای سایپا پراید 111 و تیبا.', 'فیلتر هوا پراید', 'فیلتر هوای اصلی برای سرویس دوره‌ای پراید.', 'فیلتر هوا پراید, قطعات پراید', 95000, 35, 1);

INSERT INTO vehicle_model_service (model_id, service_id) VALUES
(1, 1),
(1, 2),
(3, 1),
(4, 1),
(5, 2),
(6, 2);

INSERT INTO article_vehicle_model (article_id, model_id) VALUES
(1, 1),
(2, 5);

INSERT INTO product_vehicle_model (product_id, model_id) VALUES
(1, 1),
(2, 2);

INSERT INTO vehicle_common_problems (model_id, title_fa, title_en, slug, symptoms_fa, cause_fa, severity, category, search_keywords_fa, expert_level, verified_by_mechanic, created_by) VALUES
(1, 'روشن نشدن خودرو در صبح سرد', 'Engine won''t start on cold mornings', 'peugeot-206-cold-start-issue', 'قابل استارت زدن نیست و استارت نمی‌کشد.', 'خرابی کویل، شمع یا مشکلات برق اولیه.', 'متوسط', 'موتور', 'روشن نشدن, پژو 206, استارت نخوردن', 3, 1, 1),
(4, 'تعویض دیرهنگام روغن گیربکس', 'Delayed gearbox response', 'tiggo-5-gearbox-fluid-ageing', 'تاخیر در تعویض دنده و لغزش هنگام شتاب‌گیری.', 'مایع گیربکس فرسوده یا کثیف شده است.', 'زیاد', 'گیربکس', 'گیربکس تیگو, تعویض روغن گیربکس, تاخیر دنده', 3, 1, 1);

INSERT INTO vehicle_repair_solutions (problem_id, title_fa, title_en, description_fa, required_parts_fa, estimated_time, service_id, search_keywords_fa, expert_level, verified_by_mechanic, created_by) VALUES
(1, 'بررسی کویل و شمع پژو 206', 'Inspect 206 coil and spark plugs', 'کویل و شمع‌ها را بررسی و در صورت نیاز تعویض کنید. همچنین مسیر برق استارت را چک کنید.', 'شمع NGK, کویل اصلی پژو', '2 ساعت', 1, 'تعویض شمع پژو, بررسی کویل پژو', 3, 1, 1),
(2, 'تعویض روغن گیربکس تیگو', 'Replace Tiggo gearbox oil', 'روغن گیربکس را با روغن توصیه شده تعویض کنید و فیلتر داخلی را بررسی نمایید.', 'روغن گیربکس CVT, فیلتر گیربکس', '3 ساعت', 3, 'تعویض روغن گیربکس تیگو, تعمیر گیربکس', 3, 1, 1);

INSERT INTO vehicle_maintenance_tasks (model_id, title_fa, title_en, interval_km, interval_months, description_fa, required_parts_fa, search_keywords_fa, expert_level, verified_by_mechanic, created_by) VALUES
(1, 'تعویض روغن موتور پژو 206', 'Peugeot 206 oil change', 10000, 6, 'روغن موتور و فیلتر روغن را هر 10000 کیلومتر یا 6 ماه تعویض کنید.', 'روغن موتور 10W40, فیلتر روغن', 'تعویض روغن پژو 206, سرویس پژو 206', 2, 1, 1),
(2, 'بازبینی سیستم برق پراید', 'Pride electrical inspection', 10000, 6, 'سیم‌کشی اصلی، باتری و وضعیت اتصال‌ها را چک کنید تا از قطعی جلوگیری شود.', 'باتری 45 آمپر, پاک‌کننده اتصال', 'سیستم برق پراید, چک برق پراید', 2, 1, 1);

INSERT INTO vehicle_diagnostics (model_id, code, title_fa, title_en, description_fa, test_steps_fa, recommended_tools_fa, search_keywords_fa, expert_level, verified_by_mechanic, created_by) VALUES
(1, 'P0300', 'خطای احتراق نامنظم', 'Random misfire detected', 'این خطا نشان‌دهنده احتراق نامنظم چند سیلندر است.', '1. بازبینی شمع و کویل\n2. بررسی فشار سوخت\n3. دیاگ سنسور اکسیژن', 'دیاگ OBD2, مولتی‌متر', 'کد P0300, خطای احتراق پژو 206', 3, 1, 1),
(5, 'P0420', 'راندمان پایین کاتالیست', 'Catalyst efficiency below threshold', 'این کد نشان‌دهنده کاهش کارایی کاتالیست است که ممکن است از سنسور یا موتور نشأت گیرد.', '1. بررسی سنسور lambda\n2. بررسی سیستم اگزوز\n3. بررسی مصرف سوخت', 'دیاگ OBD2, تستر لامبدا', 'کد P0420, مشکلات تویوتا کرولا', 3, 1, 1);

INSERT INTO vehicle_ecu_info (model_id, ecu_name, ecu_type, ecu_vendor, protocol, software_version, notes_fa, notes_en, created_by) VALUES
(1, 'ECU موتور TU3', 'Engine', 'Bosch', 'CAN', 'V1.0', 'ECU موتور پژو 206 با پروتکل CAN کار می‌کند.', 'Peugeot 206 engine ECU uses CAN protocol.', 1),
(4, 'ECU گیربکس CVT', 'Transmission', 'Chery', 'CAN', 'V2.1', 'ECU گیربکس تیگو 5 در سیستم CVT حسگرهای فشار را مدیریت می‌کند.', 'Tiggo 5 CVT ECU manages pressure sensors.', 1);

INSERT INTO vehicle_dtc_codes (model_id, code, title_fa, title_en, description_fa, probable_cause_fa, repair_advice_fa, severity, created_by) VALUES
(1, 'P0171', 'سیستم مخلوط سوخت ضعیف', 'System too lean', 'موتور مخلوط سوخت را به نسبت زیاد هوا تشخیص می‌دهد.', 'نشتی هوا، سنسور MAF یا سوخت کم فشار.', 'بررسی نشت هوا و سنسور MAF، سپس تعمیر یا تعویض.', 'متوسط', 1),
(4, 'P0740', 'مشکل گشتاور تبدیل‌کننده', 'Torque converter clutch circuit malfunction', 'مشکل در مدار کلاچ مبدل گشتاور در گیربکس CVT.', 'عیب در شیر برقی یا سیم‌کشی روغن گیربکس.', 'بررسی سیم‌کشی، شیر برقی و سطح روغن گیربکس.', 'زیاد', 1);

INSERT INTO vehicle_diagnostic_tools (name, compatible_with, supported_protocols, description_fa, description_en, status) VALUES
('دستگاه دیاگ OBD2 پایه', 'OBD2', 'CAN, ISO9141', 'برای خواندن کدهای عمومی و بررسی سنسورهای پایه مناسب است.', 'Basic OBD2 tool for reading generic codes and sensor checks.', 1),
('دستگاه دیاگ پیشرفته ایران خودرو', 'OBD2, K-Line', 'CAN, K-Line, UDS', 'ابزار تخصصی برای خودروهای ایران خودرو و پژو با پشتیبانی از کدهای خاص.', 'Advanced diagnostic tool for IKCO and Peugeot models.', 1);

INSERT INTO media_library (file_name, file_path, file_type, alt_text_fa, uploaded_by) VALUES
('peugeot-206-front.jpg', '/uploads/vehicles/peugeot-206-front.jpg', 'image/jpeg', 'نمای جلویی پژو 206', 1),
('tiggo-5-engine.jpg', '/uploads/vehicles/tiggo-5-engine.jpg', 'image/jpeg', 'موتور تیگو 5', 1);

INSERT INTO media_relations (media_id, entity_type, entity_id, context, display_order) VALUES
(1, 'vehicle_model', 1, 'vehicle_image', 1),
(2, 'vehicle_model', 4, 'vehicle_image', 1);

SET FOREIGN_KEY_CHECKS = 1;

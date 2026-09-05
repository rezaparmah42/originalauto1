-- Production schema for Original East v1.0
-- Based on vehicle_knowledge_base_migration.sql
-- This file contains the structural schema only; no large seed data is included.

SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS original_east
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE original_east;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    email VARCHAR(150) NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(30) NOT NULL DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_users_phone (phone),
    UNIQUE KEY uq_users_email (email),
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

CREATE TABLE product_compatibility (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    model_id INT NOT NULL,
    year VARCHAR(20) DEFAULT NULL,
    engine_type VARCHAR(120) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_product_compatibility (product_id, model_id, year, engine_type),
    INDEX idx_product_compatibility_product_id (product_id),
    INDEX idx_product_compatibility_model_id (model_id),
    CONSTRAINT fk_product_compatibility_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_product_compatibility_model_id FOREIGN KEY (model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE ON UPDATE CASCADE
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
    user_id INT NOT NULL,
    brand VARCHAR(100) DEFAULT NULL,
    model VARCHAR(100) DEFAULT NULL,
    year VARCHAR(20) DEFAULT NULL,
    engine VARCHAR(100) DEFAULT NULL,
    vin VARCHAR(100) DEFAULT NULL,
    mileage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_vehicles_user_id (user_id),
    CONSTRAINT fk_vehicles_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE repairs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    diagnosis TEXT DEFAULT NULL,
    repair_notes TEXT DEFAULT NULL,
    cost DECIMAL(12,2) DEFAULT 0,
    status VARCHAR(50) NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_repairs_booking_id (booking_id),
    INDEX idx_repairs_status (status),
    INDEX idx_repairs_created_at (created_at),
    CONSTRAINT fk_repairs_booking_id FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE repair_parts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    repair_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    part_name VARCHAR(200) DEFAULT NULL,
    quantity INT DEFAULT 1,
    unit_price DECIMAL(12,2) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_repair_parts_repair_id (repair_id),
    INDEX idx_repair_parts_product_id (product_id),
    CONSTRAINT fk_repair_parts_repair_id FOREIGN KEY (repair_id) REFERENCES repairs(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_repair_parts_product_id FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    service_id INT DEFAULT NULL,
    rating INT DEFAULT NULL,
    comment TEXT DEFAULT NULL,
    status VARCHAR(30) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_reviews_user_id (user_id),
    INDEX idx_reviews_service_id (service_id),
    INDEX idx_reviews_status (status),
    INDEX idx_reviews_created_at (created_at),
    CONSTRAINT fk_reviews_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_service_id FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(120) NOT NULL,
    phone VARCHAR(60) NOT NULL,
    email VARCHAR(150) DEFAULT NULL,
    subject VARCHAR(200) DEFAULT NULL,
    message TEXT NOT NULL,
    status VARCHAR(60) NOT NULL DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_customer_inquiries_user_id (user_id),
    INDEX idx_customer_inquiries_status (status),
    CONSTRAINT fk_customer_inquiries_user_id FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE homepage_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(120) NOT NULL,
    title VARCHAR(200) DEFAULT NULL,
    content TEXT DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_homepage_sections_key (section_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE seo_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(120) NOT NULL,
    meta_title VARCHAR(200) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    meta_keywords TEXT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_seo_settings_key (page_key)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;


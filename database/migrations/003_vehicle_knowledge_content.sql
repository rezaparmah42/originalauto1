-- OriginalAuto vehicle knowledge incremental migration
CREATE TABLE IF NOT EXISTS knowledge_source_documents (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 source_path VARCHAR(500) NOT NULL,
 source_name VARCHAR(255) NOT NULL,
 source_hash CHAR(64) NOT NULL,
 source_type VARCHAR(40) NOT NULL DEFAULT 'pdf',
 language VARCHAR(20) DEFAULT 'fa',
 page_count INT UNSIGNED DEFAULT NULL,
 extracted_text LONGTEXT NULL,
 status ENUM('imported','mapped','draft','published','rejected') NOT NULL DEFAULT 'imported',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_knowledge_source_hash (source_hash),
 KEY idx_knowledge_source_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS vehicle_knowledge_contents (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 vehicle_model_id INT NOT NULL,
 service_id INT NULL,
 source_document_id BIGINT UNSIGNED NULL,
 content_type VARCHAR(60) NOT NULL,
 slug VARCHAR(191) NOT NULL,
 title_fa VARCHAR(255) NOT NULL,
 title_en VARCHAR(255) NULL,
 excerpt TEXT NULL,
 content_fa LONGTEXT NULL,
 content_en LONGTEXT NULL,
 seo_title_fa VARCHAR(255) NULL,
 seo_description_fa TEXT NULL,
 status ENUM('draft','review','published','rejected') NOT NULL DEFAULT 'draft',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 UNIQUE KEY uq_vehicle_knowledge_slug (slug),
 KEY idx_vehicle_knowledge_model_type (vehicle_model_id, content_type),
 KEY idx_vehicle_knowledge_service (service_id),
 KEY idx_vehicle_knowledge_status (status),
 CONSTRAINT fk_vehicle_knowledge_model FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE,
 CONSTRAINT fk_vehicle_knowledge_service FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE SET NULL,
 CONSTRAINT fk_vehicle_knowledge_source FOREIGN KEY (source_document_id) REFERENCES knowledge_source_documents(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

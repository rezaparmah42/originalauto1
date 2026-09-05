CREATE DATABASE IF NOT EXISTS original_east
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE original_east;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    phone VARCHAR(30),
    email VARCHAR(150),
    password VARCHAR(255),
    role VARCHAR(30) DEFAULT 'customer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vehicles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    brand VARCHAR(100),
    model VARCHAR(100),
    year VARCHAR(20),
    engine VARCHAR(100),
    vin VARCHAR(100),
    mileage INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE services (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150),
    slug VARCHAR(150),
    brand_id INT DEFAULT NULL,
    category VARCHAR(80) DEFAULT 'general',
    description TEXT,
    duration VARCHAR(80) DEFAULT NULL,
    price VARCHAR(50),
    image VARCHAR(255) DEFAULT NULL,
    seo_title VARCHAR(200) DEFAULT NULL,
    seo_description TEXT DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE vehicle_brands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120),
    slug VARCHAR(120),
    category VARCHAR(80),
    description TEXT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    slug VARCHAR(200),
    category VARCHAR(120) DEFAULT 'general',
    author VARCHAR(120) DEFAULT NULL,
    content LONGTEXT,
    image VARCHAR(255) DEFAULT NULL,
    seo_title VARCHAR(200) DEFAULT NULL,
    seo_description TEXT DEFAULT NULL,
    meta_description TEXT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customer_inquiries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    name VARCHAR(120),
    phone VARCHAR(60),
    email VARCHAR(150) DEFAULT NULL,
    subject VARCHAR(200),
    message TEXT,
    status VARCHAR(60) DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(200),
    slug VARCHAR(200),
    description TEXT,
    price DECIMAL(12,2),
    stock INT DEFAULT 0,
    image VARCHAR(255) DEFAULT NULL,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE homepage_sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    section_key VARCHAR(120),
    title VARCHAR(200),
    content TEXT,
    status TINYINT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE seo_settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    page_key VARCHAR(120),
    meta_title VARCHAR(200),
    meta_description TEXT,
    meta_keywords TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE media_library (
    id INT AUTO_INCREMENT PRIMARY KEY,
    file_name VARCHAR(255),
    file_path VARCHAR(255),
    file_type VARCHAR(80),
    alt_text VARCHAR(255) DEFAULT NULL,
    uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    vehicle_id INT,
    service_id INT,
    problem TEXT,
    status VARCHAR(50) DEFAULT 'new',
    booking_date DATETIME,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE repairs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT,
    diagnosis TEXT,
    repair_notes TEXT,
    cost DECIMAL(12,2) DEFAULT 0,
    status VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    total DECIMAL(12,2),
    status VARCHAR(50) DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    service_id INT,
    rating INT,
    comment TEXT,
    status VARCHAR(30) DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

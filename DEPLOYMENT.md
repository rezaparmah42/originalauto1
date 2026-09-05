# Original Shargh - Deployment Guide

## Table of Contents
1. [System Requirements](#system-requirements)
2. [Pre-Deployment Checklist](#pre-deployment-checklist)
3. [Installation Steps](#installation-steps)
4. [Database Setup](#database-setup)
5. [First Admin Account](#first-admin-account)
6. [Upload Directory Configuration](#upload-directory-configuration)
7. [Environment Configuration](#environment-configuration)
8. [Security Hardening](#security-hardening)
9. [Post-Deployment Testing](#post-deployment-testing)
10. [Troubleshooting](#troubleshooting)

---

## System Requirements

### Minimum Requirements
- **PHP:** 7.4 or higher (8.0+ recommended)
- **MySQL:** 5.7 or higher (8.0+ recommended)
- **Web Server:** Apache 2.4+ with mod_rewrite enabled
- **Operating System:** Linux, Windows, or macOS

### Required PHP Extensions
- `php-pdo` - Database abstraction
- `php-pdo-mysql` - MySQL driver
- `php-mbstring` - Multi-byte string support
- `php-json` - JSON support
- `php-fileinfo` - File type detection
- `php-gd` - Image processing (for future image features)
- `php-openssl` - SSL/TLS support

### Recommended PHP Extensions
- `php-bcmath` - Arbitrary precision arithmetic (for crypto)
- `php-curl` - HTTP client
- `php-zip` - Archive handling
- `php-xml` - XML processing

### Verify Installation
```bash
php -v                          # Check PHP version
php -m | grep pdo               # Verify PDO extension
php -m | grep mbstring          # Verify mbstring
mysql -V                        # Check MySQL version
apache2ctl -v                   # Check Apache version
```

---

## Pre-Deployment Checklist

- [ ] PHP version >= 7.4
- [ ] MySQL version >= 5.7
- [ ] All required extensions installed
- [ ] Web server configured
- [ ] Domain/URL configured
- [ ] SSL/HTTPS certificate (production)
- [ ] Database backup strategy planned
- [ ] Backup location configured
- [ ] Error logging configured
- [ ] Monitoring/alerting setup

---

## Installation Steps

### Step 1: Extract Project Files

```bash
# Clone or extract the project
cd /var/www/html
unzip originalshargh.zip
cd originalshargh
```

### Step 2: Set Directory Permissions

```bash
# Set permissions for web server access
chmod 755 .
chmod 755 public
chmod 755 storage
chmod 755 storage/backups
chmod 755 public/uploads
chmod 755 public/uploads/*

# On Windows/XAMPP, ensure directories are writable
# Right-click folder → Properties → Security → Edit → Add read/write permissions
```

### Step 3: Copy Configuration File

```bash
# Copy environment example to .env (if needed)
cp .env.example .env

# Edit .env with your actual values
nano .env
```

### Step 4: Verify PHP Configuration

Check `php.ini` settings:

```ini
; Required minimum settings
upload_max_filesize = 10M
post_max_size = 10M
max_execution_time = 300
max_input_time = 300
memory_limit = 256M

; Session settings
session.cookie_httponly = On
session.cookie_samesite = Lax
session.cookie_secure = On        ; Enable on HTTPS only
session.use_strict_mode = On
session.use_only_cookies = On

; Error logging
error_reporting = E_ALL
log_errors = On
error_log = /var/log/php-errors.log
```

---

## Database Setup

### Step 1: Create Database

```bash
mysql -u root -p
```

```sql
CREATE DATABASE IF NOT EXISTS original_east
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```

### Step 2: Import Schema

```bash
# Import production schema
mysql -u root -p original_east < database/production_schema.sql

# Verify tables were created
mysql -u root -p original_east -e "SHOW TABLES;"
```

Expected tables (30+):
- users
- vehicle_brands, vehicle_models
- products, orders
- services, bookings
- articles, media_library
- settings, homepage_sections
- And more...

### Step 3: Import Seed Data

```bash
# Import initial data (admin user, services, vehicle brands)
mysql -u root -p original_east < database/seed.sql

# Verify admin user was created
mysql -u root -p original_east -e "SELECT id, name, email, role FROM users LIMIT 5;"
```

Expected output:
```
+----+---------------------+------------------------+-------+
| id | name                | email                  | role  |
+----+---------------------+------------------------+-------+
|  1 | System Administrator| admin@example.com      | admin |
+----+---------------------+------------------------+-------+
```

### Step 4: Database Permissions

```bash
# Set proper user permissions (production)
mysql -u root -p
```

```sql
-- Create database user (for production)
CREATE USER 'originalshargh'@'localhost' IDENTIFIED BY 'strong_password_here';
GRANT ALL PRIVILEGES ON original_east.* TO 'originalshargh'@'localhost';
FLUSH PRIVILEGES;

-- Remove default MySQL users
DROP USER ''@'localhost';
DROP USER ''@'%%';
FLUSH PRIVILEGES;
```

---

## First Admin Account

### Initial Credentials

After seeding, the first admin account is:

- **Email:** admin@example.com
- **Initial Password:** admin123 (bcrypt hashed)
- **Role:** admin

### Change Password Immediately

1. Navigate to http://your-domain/admin/login
2. Login with credentials above
3. Go to admin settings/profile
4. Change password to a strong password
5. Re-login with new password

### Delete Default Account (Optional)

If using a different admin user, delete the default:

```bash
mysql -u root -p original_east
```

```sql
DELETE FROM users WHERE email = 'admin@example.com';
```

### Create Additional Admin Users

```sql
INSERT INTO users (name, email, phone, password, role) VALUES
('Admin Name', 'admin2@example.com', '09000000000', 
 '$2y$10$...hashed_password...', 'admin');
```

Use PHP to generate hash:
```php
echo password_hash('yourpassword', PASSWORD_DEFAULT);
```

---

## Upload Directory Configuration

### Directory Structure

```
public/uploads/
├── products/          # Product images
├── services/          # Service photos
├── articles/          # Article images
├── avatars/          # User profile pictures
├── temp/             # Temporary files
└── .htaccess         # Security rules
```

### Permissions

```bash
# Set proper permissions
chmod 755 public/uploads
chmod 755 public/uploads/*
chown www-data:www-data public/uploads -R  # Linux/Apache
```

### Security (.htaccess)

The `.htaccess` file in `public/uploads/` prevents PHP execution:

```apache
# Prevent PHP execution
<FilesMatch "\.php$">
    Deny from all
</FilesMatch>

# Disable script execution
AddType text/plain .php .php3 .php4 .php5 .php6 .php7
```

### On Windows/XAMPP

If using Windows/XAMPP:
1. Ensure folder has full permissions
2. IIS may need alternative configuration (web.config)
3. Test by attempting to access a PHP file in uploads/

---

## Environment Configuration

### Configuration File

Configuration is managed through environment variables in `config/config.php`:

```php
// Automatically read from .env or use defaults
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('SITE_URL', getenv('APP_URL') ?: 'http://localhost');
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'original_east');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DEBUG', filter_var(getenv('APP_DEBUG') ?: false, FILTER_VALIDATE_BOOLEAN));
```

### Using .env File

Create `.env` file in project root:

```bash
# Copy template
cp .env.example .env

# Edit with your values
nano .env
```

---

## Security Hardening

### 1. HTTPS/SSL

Enable HTTPS for all traffic (production):

```apache
<VirtualHost *:443>
    ServerName originalshargh.com
    SSLEngine On
    SSLCertificateFile /path/to/cert.pem
    SSLCertificateKeyFile /path/to/key.pem
</VirtualHost>
```

### 2. Database Security

- Use strong password (min 16 characters)
- Create separate database user (not root)
- Regular backups
- Implement automated backup rotation

### 3. File Permissions

```bash
chmod 755 public
chmod 700 storage
chmod 700 public/uploads
```

---

## Post-Deployment Testing

### 1. Application Smoke Test

```bash
php smoke_test_v2.php
```

Expected: 90%+ pass rate

### 2. Database Connectivity

```bash
mysql -u root -p original_east -e "SELECT COUNT(*) FROM users;"
```

### 3. Test Authentication

1. Open http://localhost/originalshargh/admin/login
2. Login with admin@example.com / admin123
3. Change password immediately

### 4. Test Key Pages

- Homepage (/)
- Services (/services)
- Booking (/booking)
- Login (/login)

---

## Quick Start Summary

```bash
# 1. Create database
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS original_east CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"

# 2. Import schema
mysql -u root -p original_east < database/production_schema.sql

# 3. Import seed data
mysql -u root -p original_east < database/seed.sql

# 4. Test
php smoke_test_v2.php

# 5. Access application
# Admin: http://localhost/originalshargh/admin/login
```

---

**Version:** 1.0.0  
**Status:** Production Ready

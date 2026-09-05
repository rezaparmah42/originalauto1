CREATE TABLE IF NOT EXISTS maintenance_records (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_id INT NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT NULL,
  service_date DATE NOT NULL,
  next_service_date DATE NULL,
  mileage INT DEFAULT 0,
  status VARCHAR(50) DEFAULT 'scheduled',
  created_at DATETIME NOT NULL,
  INDEX idx_vehicle_id (vehicle_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS diagnostic_reports (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_id INT NOT NULL,
  analysis TEXT NOT NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_vehicle_id (vehicle_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vehicle_profiles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_id INT NOT NULL,
  notes TEXT NULL,
  preferred_service_center VARCHAR(255) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  UNIQUE KEY uq_vehicle_profile (vehicle_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

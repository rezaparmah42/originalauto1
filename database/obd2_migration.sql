CREATE TABLE IF NOT EXISTS obd_error_codes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  code VARCHAR(20) NOT NULL UNIQUE,
  system VARCHAR(100) DEFAULT NULL,
  title_fa VARCHAR(255) NOT NULL,
  description_fa TEXT DEFAULT NULL,
  severity VARCHAR(20) DEFAULT 'unknown',
  possible_causes TEXT DEFAULT NULL,
  recommended_actions TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS diagnostic_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  vehicle_id INT NOT NULL,
  user_id INT NOT NULL,
  device_type VARCHAR(50) DEFAULT 'ELM327',
  connection_status VARCHAR(50) DEFAULT 'pending',
  created_at DATETIME NOT NULL,
  INDEX idx_vehicle_id (vehicle_id),
  INDEX idx_user_id (user_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS diagnostic_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  session_id INT NOT NULL,
  error_code_id INT NOT NULL,
  raw_data TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL,
  INDEX idx_session_id (session_id),
  INDEX idx_error_code_id (error_code_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS diagnostic_knowledge (
  id INT AUTO_INCREMENT PRIMARY KEY,
  dtc_code VARCHAR(32) NOT NULL,
  title VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  severity VARCHAR(50) DEFAULT 'unknown',
  possible_causes TEXT DEFAULT NULL,
  recommended_actions TEXT DEFAULT NULL,
  created_at DATETIME NOT NULL,
  UNIQUE KEY uq_dtc (dtc_code)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS vehicle_symptoms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(255) NOT NULL,
  description TEXT DEFAULT NULL,
  category VARCHAR(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS repair_recommendations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  diagnostic_id INT NOT NULL,
  part_name VARCHAR(255) NOT NULL,
  action VARCHAR(255) NOT NULL,
  priority INT DEFAULT 0,
  INDEX idx_diagnostic_id (diagnostic_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

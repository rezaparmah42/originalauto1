-- Consolidated API security migration (Patch 7.6 fix)

CREATE TABLE IF NOT EXISTS api_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(191) NOT NULL DEFAULT 'Mobile App',
  token VARCHAR(128) NOT NULL,
  revoked TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME DEFAULT NULL,
  last_used_at DATETIME DEFAULT NULL,
  INDEX idx_api_tokens_user_id (user_id),
  INDEX idx_api_tokens_token (token(64)),
  INDEX idx_api_tokens_revoked (revoked),
  CONSTRAINT fk_api_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS api_devices (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  token_id INT NOT NULL,
  device_name VARCHAR(191) DEFAULT 'Unknown Device',
  platform VARCHAR(100) DEFAULT 'unknown',
  last_active DATETIME DEFAULT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_api_devices_user_id (user_id),
  INDEX idx_api_devices_token_id (token_id),
  CONSTRAINT fk_api_devices_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT fk_api_devices_token FOREIGN KEY (token_id) REFERENCES api_tokens(id) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS api_logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  endpoint VARCHAR(255) NOT NULL,
  method VARCHAR(20) NOT NULL,
  ip_address VARCHAR(50) NOT NULL,
  response_code INT NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  INDEX idx_api_logs_user_id (user_id),
  INDEX idx_api_logs_endpoint (endpoint(128)),
  INDEX idx_api_logs_created_at (created_at),
  CONSTRAINT fk_api_logs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Ensure token column length index for faster lookups
ALTER TABLE api_tokens ADD INDEX IF NOT EXISTS idx_token_full (token(128));

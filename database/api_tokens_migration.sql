-- Mobile API Authentication Migration

CREATE TABLE IF NOT EXISTS api_tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  name VARCHAR(191) NOT NULL DEFAULT 'Mobile App',
  token VARCHAR(128) NOT NULL,
  revoked TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  expires_at DATETIME NOT NULL,
  last_used_at DATETIME DEFAULT NULL,
  INDEX idx_api_tokens_user_id (user_id),
  INDEX idx_api_tokens_token (token),
  INDEX idx_api_tokens_revoked (revoked),
  CONSTRAINT fk_api_tokens_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE ON UPDATE CASCADE
);

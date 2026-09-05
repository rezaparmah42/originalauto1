-- Workshop Management Migration
-- Tables: technicians, workshop_tasks, repair_notes, technician_activity

CREATE TABLE IF NOT EXISTS technicians (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT DEFAULT NULL,
  name VARCHAR(191) NOT NULL,
  phone VARCHAR(50) DEFAULT NULL,
  specialization VARCHAR(191) DEFAULT NULL,
  status VARCHAR(50) DEFAULT 'active',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS workshop_tasks (
  id INT AUTO_INCREMENT PRIMARY KEY,
  repair_id INT NOT NULL,
  technician_id INT DEFAULT NULL,
  title VARCHAR(191) NOT NULL,
  description TEXT,
  priority VARCHAR(20) DEFAULT 'normal',
  status VARCHAR(50) DEFAULT 'pending',
  started_at DATETIME DEFAULT NULL,
  completed_at DATETIME DEFAULT NULL
);

CREATE TABLE IF NOT EXISTS repair_notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  repair_id INT NOT NULL,
  user_id INT DEFAULT NULL,
  note TEXT NOT NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS technician_activity (
  id INT AUTO_INCREMENT PRIMARY KEY,
  technician_id INT NOT NULL,
  action VARCHAR(191) NOT NULL,
  description TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

COMMIT;

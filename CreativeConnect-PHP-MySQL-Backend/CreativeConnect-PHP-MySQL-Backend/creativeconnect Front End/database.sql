CREATE DATABASE IF NOT EXISTS creativeconnect
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE creativeconnect;

CREATE TABLE IF NOT EXISTS users (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  full_name VARCHAR(100) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  company VARCHAR(120) NULL,
  password_hash VARCHAR(255) NOT NULL,
  role ENUM('client', 'admin') NOT NULL DEFAULT 'client',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS project_requests (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  user_id INT UNSIGNED NOT NULL,
  contact_name VARCHAR(100) NOT NULL,
  contact_email VARCHAR(190) NOT NULL,
  company VARCHAR(120) NOT NULL,
  service VARCHAR(50) NOT NULL,
  preferred_deadline DATE NOT NULL,
  budget DECIMAL(10,2) NOT NULL,
  brief TEXT NOT NULL,
  status ENUM('submitted', 'under-review', 'in-progress', 'completed', 'cancelled') NOT NULL DEFAULT 'submitted',
  created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_project_request_user
    FOREIGN KEY (user_id) REFERENCES users(id)
    ON DELETE CASCADE,
  INDEX idx_project_requests_user_id (user_id),
  INDEX idx_project_requests_status (status),
  INDEX idx_project_requests_created_at (created_at)
) ENGINE=InnoDB;

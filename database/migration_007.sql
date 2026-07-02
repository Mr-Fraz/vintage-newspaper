-- Migration 007: Author bio + newsletter subscribers
-- Run after migrations 001-006

ALTER TABLE users ADD COLUMN IF NOT EXISTS bio TEXT NULL AFTER avatar;
ALTER TABLE users ADD COLUMN IF NOT EXISTS title VARCHAR(150) NULL AFTER bio;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) NOT NULL UNIQUE,
    status ENUM('active', 'unsubscribed') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- End migration 007
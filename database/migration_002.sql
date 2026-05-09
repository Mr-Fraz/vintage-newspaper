-- Migration 002: Add revisions, tags, article_tags, activity_log, password_resets
-- Also add seo fields and image column to articles

-- Add article columns if they do not already exist
ALTER TABLE articles ADD COLUMN IF NOT EXISTS status ENUM('draft','pending','scheduled','published','archived') NOT NULL DEFAULT 'draft';
ALTER TABLE articles ADD COLUMN IF NOT EXISTS publish_at DATETIME NULL;
ALTER TABLE articles ADD COLUMN IF NOT EXISTS seo_title VARCHAR(255) NULL;
ALTER TABLE articles ADD COLUMN IF NOT EXISTS meta_description VARCHAR(500) NULL;
ALTER TABLE articles ADD COLUMN IF NOT EXISTS og_image VARCHAR(255) NULL;
ALTER TABLE articles ADD COLUMN IF NOT EXISTS views INT UNSIGNED NOT NULL DEFAULT 0;
ALTER TABLE articles ADD COLUMN IF NOT EXISTS image VARCHAR(255) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS article_revisions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  article_id INT NOT NULL,
  user_id INT NULL,
  title VARCHAR(255) NULL,
  content LONGTEXT,
  seo_title VARCHAR(255) NULL,
  meta_description VARCHAR(500) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (article_id),
  CONSTRAINT fk_rev_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS tags (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  slug VARCHAR(120) NOT NULL UNIQUE,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS article_tag (
  article_id INT NOT NULL,
  tag_id INT NOT NULL,
  PRIMARY KEY (article_id, tag_id),
  CONSTRAINT fk_at_article FOREIGN KEY (article_id) REFERENCES articles(id) ON DELETE CASCADE,
  CONSTRAINT fk_at_tag FOREIGN KEY (tag_id) REFERENCES tags(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS activity_log (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  action VARCHAR(100) NOT NULL,
  entity_type VARCHAR(50) NULL,
  entity_id INT NULL,
  meta JSON NULL,
  ip VARCHAR(45) NULL,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(255) NOT NULL,
  token VARCHAR(128) NOT NULL,
  expires_at DATETIME NOT NULL,
  used TINYINT DEFAULT 0,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  INDEX (email),
  INDEX (token)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- End migration 002

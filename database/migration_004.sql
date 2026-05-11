-- Migration 004: Media library with alt-text and image variants

CREATE TABLE IF NOT EXISTS media (
    id INT AUTO_INCREMENT PRIMARY KEY,
    filename VARCHAR(255) NOT NULL,          -- original: abc123.jpg
    filename_thumb VARCHAR(255) NOT NULL,    -- thumb_abc123.jpg (300x200)
    filename_medium VARCHAR(255) NOT NULL,   -- medium_abc123.jpg (800x500)
    alt_text VARCHAR(255) DEFAULT '',
    uploaded_by INT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_media_user FOREIGN KEY (uploaded_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add media_id FK to articles (keep image column for backward compat)
ALTER TABLE articles ADD COLUMN IF NOT EXISTS media_id INT NULL;
ALTER TABLE articles ADD COLUMN IF NOT EXISTS image_alt VARCHAR(255) DEFAULT '';
ALTER TABLE articles ADD CONSTRAINT fk_article_media
    FOREIGN KEY (media_id) REFERENCES media(id) ON DELETE SET NULL;
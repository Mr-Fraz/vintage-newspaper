-- Migration 006: Multi-language article support
-- Run after migrations 001-005

-- USE vintage_newspaper;

-- Add language column to articles (marks the "original" / base language)
ALTER TABLE articles
    ADD COLUMN lang VARCHAR(10) NOT NULL DEFAULT 'en' AFTER slug,
    ADD INDEX idx_articles_lang (lang);

-- Translations table: each row = one translation of a parent article
CREATE TABLE IF NOT EXISTS article_translations (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    article_id    INT NOT NULL,                        -- FK to articles.id (the original)
    lang          VARCHAR(10) NOT NULL,                -- e.g. 'ur', 'ar', 'fr'
    title         VARCHAR(255) NOT NULL,
    slug          VARCHAR(255) NOT NULL,
    content       LONGTEXT,
    excerpt       TEXT,
    seo_title     VARCHAR(255) DEFAULT NULL,
    meta_description TEXT DEFAULT NULL,
    status        ENUM('draft','pending','published','archived') DEFAULT 'draft',
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_article_lang (article_id, lang),
    UNIQUE KEY uq_slug_lang (slug, lang),
    INDEX idx_trans_article (article_id),
    INDEX idx_trans_lang_status (lang, status),
    CONSTRAINT fk_trans_article FOREIGN KEY (article_id)
        REFERENCES articles(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- End migration 006
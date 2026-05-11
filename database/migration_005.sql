-- Migration 005: Performance indexes
-- Run after migrations 001-004

USE vintage_newspaper;

-- Replace single status index with composite (status + date)
ALTER TABLE articles DROP INDEX IF EXISTS idx_articles_status;
CREATE INDEX idx_articles_status_date ON articles(status, created_at DESC);

-- Composite for category page queries
CREATE INDEX idx_articles_category_status_date ON articles(category_id, status, created_at DESC);

-- FULLTEXT for search (replaces LIKE scans)
ALTER TABLE articles ADD FULLTEXT INDEX ft_articles_search (title, content);

-- Comments: moderation list sorting
CREATE INDEX idx_comments_status_date ON comments(status, created_at DESC);

-- Activity log: user history queries
CREATE INDEX idx_activity_user_date ON activity_log(user_id, created_at DESC);

-- Scheduled publishing (cron-publish.php)
CREATE INDEX idx_articles_publish_at ON articles(publish_at, status);

-- Password resets: cleanup expired tokens
CREATE INDEX idx_resets_expires ON password_resets(expires_at, used);

-- End migration 005
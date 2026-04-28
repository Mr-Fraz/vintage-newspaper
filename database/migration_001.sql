-- Migration: Add categories and enhance schema
-- Created: 2024
-- Purpose: Add category support and additional article fields
CREATE DATABASE IF NOT EXISTS vintage_newspaper;
USE vintage_newspaper;
-- Create categories table
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE IF NOT EXISTS articles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE,
    content TEXT NOT NULL,

    -- These columns are required BEFORE migration runs
    category_id INT DEFAULT NULL,
    author_id INT DEFAULT NULL,
    status ENUM('draft', 'published', 'archived') DEFAULT 'published',
    featured_image VARCHAR(255) DEFAULT NULL,

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Add foreign key constraints
ALTER TABLE articles 
ADD CONSTRAINT fk_articles_category 
FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL;

ALTER TABLE articles 
ADD CONSTRAINT fk_articles_author 
FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL;
ALTER TABLE articles ADD excerpt TEXT;

-- Insert default categories
INSERT IGNORE INTO categories (name, slug, description) VALUES
('Breaking News', 'breaking-news', 'Latest breaking news stories'),
('Sports', 'sports', 'Sports news and updates'),
('Entertainment', 'entertainment', 'Entertainment and celebrity news'),
('Technology', 'technology', 'Technology and innovation'),
('Business', 'business', 'Business and finance news'),
('World', 'world', 'World news and international stories');

-- Create indexes for better performance
CREATE INDEX idx_articles_category ON articles(category_id);
CREATE INDEX idx_articles_author ON articles(author_id);
CREATE INDEX idx_articles_status ON articles(status);
CREATE INDEX idx_categories_slug ON categories(slug);
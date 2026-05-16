<?php
require_once __DIR__ . '/database.php';
date_default_timezone_set('Asia/Karachi');
// Site settings
define('SITE_NAME', $_ENV['SITE_NAME']);

// Auto-detect HTTPS — fixes CSS mixed content block on HTTPS
$_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
$_rawUrl    = rtrim($_ENV['SITE_URL'], '/');
$_fixedUrl  = preg_replace('#^https?://#', $_protocol . '://', $_rawUrl);
define('SITE_URL', $_fixedUrl);
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL']);

// Pagination
define('POSTS_PER_PAGE', 10);

// Upload settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/articles/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// JWT secret for API tokens (override via environment variable in production)
if (isset($_ENV['JWT_SECRET']) && !empty($_ENV['JWT_SECRET'])) {
    define('JWT_SECRET', $_ENV['JWT_SECRET']);
} else {
    define('JWT_SECRET', 'change-this-default-secret');
}

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database instance
$db = Database::getInstance()->getConnection();
if (isset($_ENV['APP_ENV']) && $_ENV['APP_ENV'] === 'production') {
    define('ARTICLE_URL', SITE_URL . '/pages/article.php?id=');
    define('CATEGORY_URL', SITE_URL . '/pages/category.php?slug=');
} else {
    define('ARTICLE_URL', SITE_URL . '/article/');
    define('CATEGORY_URL', SITE_URL . '/category/');
}
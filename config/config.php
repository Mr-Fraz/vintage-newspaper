<?php
require_once __DIR__ . '/database.php';

// Site settings
define('SITE_NAME', $_ENV['SITE_NAME']);
define('SITE_URL', $_ENV['SITE_URL']);
define('ADMIN_EMAIL', $_ENV['ADMIN_EMAIL']);

// Pagination
define('POSTS_PER_PAGE', 10);

// Upload settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/articles/');
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif']);

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Get database instance
$db = Database::getInstance()->getConnection();
?>

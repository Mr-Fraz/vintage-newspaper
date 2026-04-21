<?php
require_once __DIR__ . '/env.php';

$conn = new mysqli(
    env('DB_HOST', 'localhost'),
    env('DB_USER', 'root'),
    env('DB_PASSWORD', ''),
    env('DB_NAME', 'vintage_news')
);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8mb4");
?>
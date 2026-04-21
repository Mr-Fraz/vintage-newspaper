<?php
/**
 * Load environment variables from .env file
 */

$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    die('Error: .env file not found. Please create one from .env.example');
}

$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    // Skip comments
    if (strpos($line, '#') === 0) {
        continue;
    }
    
    // Parse key=value
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $_ENV[$key] = $value;
        putenv("$key=$value");
    }
}

/**
 * Helper function to get environment variable
 */
function env($key, $default = null) {
    return $_ENV[$key] ?? $default;
}

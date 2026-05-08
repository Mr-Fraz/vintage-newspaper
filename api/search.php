<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/validation.php';
require_once __DIR__ . '/../functions/helpers.php';

// Basic rate limiting: 30 requests per minute per IP for search
if (Helper::rateLimitExceeded('api_search', 30, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests, try again later']);
    exit;
}

$query = isset($_GET['q']) ? Validate::sanitize($_GET['q']) : '';

if (empty($query)) {
    echo json_encode(['success' => false, 'message' => 'Query required']);
    exit;
}

$results = DB::searchArticles($query);

echo json_encode([
    'success' => true,
    'count' => count($results),
    'results' => $results
]);
?>

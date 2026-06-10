<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/validation.php';
require_once __DIR__ . '/../functions/helpers.php';

// Rate limit: 60 req/min
if (Helper::rateLimitExceeded('api_search', 60, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests, try again later']);
    exit;
}

$query = isset($_GET['q']) ? Validate::sanitize($_GET['q']) : '';
$mode  = isset($_GET['mode']) ? $_GET['mode'] : 'search'; // 'suggest' or 'search'

// --- Autocomplete / suggestions ---
if ($mode === 'suggest') {
    if (strlen($query) < 2) {
        echo json_encode(['success' => true, 'suggestions' => []]);
        exit;
    }
    $suggestions = DB::searchSuggestions($query, 6);
    echo json_encode(['success' => true, 'suggestions' => $suggestions]);
    exit;
}

// --- Full search with filters ---
$filters = [];

if (!empty($_GET['date_from'])) $filters['date_from'] = Validate::sanitize($_GET['date_from']);
if (!empty($_GET['date_to']))   $filters['date_to']   = Validate::sanitize($_GET['date_to']);
if (!empty($_GET['author']))    $filters['author']    = Validate::sanitize($_GET['author']);
if (!empty($_GET['category_id'])) $filters['category_id'] = (int)$_GET['category_id'];
if (!empty($_GET['sort']))      $filters['sort']      = in_array($_GET['sort'], ['relevance','date_desc','date_asc']) ? $_GET['sort'] : 'relevance';

if (empty($query) && empty($filters)) {
    echo json_encode(['success' => false, 'message' => 'Query or at least one filter required']);
    exit;
}

$results = DB::searchArticles($query, $filters);

echo json_encode([
    'success' => true,
    'count'   => count($results),
    'results' => $results
]);
<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/validation.php';

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

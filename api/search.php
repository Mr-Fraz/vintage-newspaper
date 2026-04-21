<?php
require('../config/database.php');
require('../functions/helpers.php');

// Validate input
$q = isset($_GET['q']) ? sanitizeInput($_GET['q']) : '';

if (empty($q) || strlen($q) < 2) {
    echo json_encode(['error' => 'Search query too short']);
    exit;
}

// Use proper prepared statement with LIKE
$search = '%' . $q . '%';
$stmt = $conn->prepare("SELECT id, title FROM articles WHERE title LIKE ? OR content LIKE ? LIMIT 10");

if (!$stmt) {
    echo json_encode(['error' => 'Database error']);
    exit;
}

$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

$results = [];
while ($row = $result->fetch_assoc()) {
    $results[] = [
        'id' => $row['id'],
        'title' => escape($row['title'])
    ];
}

$stmt->close();

// Output as JSON
header('Content-Type: application/json');
echo json_encode($results);

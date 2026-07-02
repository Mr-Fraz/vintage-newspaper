<?php
header('Content-Type: application/json');

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/validation.php';
require_once __DIR__ . '/../functions/helpers.php';

// Rate limit: 10 subscribe attempts / minute per session bucket
if (Helper::rateLimitExceeded('api_newsletter', 10, 60)) {
    http_response_code(429);
    echo json_encode(['success' => false, 'message' => 'Too many requests. Please try again shortly.']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if (!Validate::email($email)) {
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

$ok = DB::subscribeNewsletter($email);

if ($ok) {
    echo json_encode(['success' => true, 'message' => 'Subscribed! Watch your post for our next edition.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Something went wrong. Please try again.']);
}
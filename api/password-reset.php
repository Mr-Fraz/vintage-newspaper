<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/helpers.php';
header('Content-Type: application/json');

// Rate limit: 5 requests per hour per IP for password reset
if (Helper::rateLimitExceeded('pw_reset', 5, 3600)) {
    http_response_code(429);
    echo json_encode(['error' => 'Too many requests, try again later']);
    exit;
}

$email = trim($_POST['email'] ?? '');
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid email']);
    exit;
}

// Check user exists
$stmt = $db->prepare("SELECT id, email, username FROM users WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    // Do not reveal whether email exists
    echo json_encode(['success' => true, 'message' => 'If an account exists, a reset email has been sent']);
    exit;
}

// Create reset token
$token = DB::createPasswordReset($email, 3600);
if (!$token) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create reset token']);
    exit;
}

// Prepare reset link
$resetLink = rtrim(SITE_URL, '/') . '/pages/password-reset.php?token=' . urlencode($token);

// Send email (basic mail()). In production, configure SMTP.
$subject = 'Password reset request';
$message = "Hello {$user['username']},\n\nWe received a request to reset your password. Use the link below to reset it (valid for 1 hour):\n\n{$resetLink}\n\nIf you did not request this, ignore this message.\n";
$headers = 'From: ' . ADMIN_EMAIL . "\r\n" . 'Reply-To: ' . ADMIN_EMAIL . "\r\n";
@mail($email, $subject, $message, $headers);

// Log activity
if (method_exists('DB', 'logActivity')) {
    DB::logActivity($user['id'], 'password_reset_requested', 'user', $user['id'], null, $_SERVER['REMOTE_ADDR'] ?? null);
}

echo json_encode(['success' => true, 'message' => 'If an account exists, a reset email has been sent']);

?>
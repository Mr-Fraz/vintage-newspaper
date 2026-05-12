<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';
header('Content-Type: application/json');

// TEMP DEBUG
$reset = DB::getPasswordReset($_POST['token'] ?? '');
$token = $_POST['token'] ?? '';
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if (empty($token) || empty($password) || empty($confirm)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing parameters']);
    exit;
}

if ($password !== $confirm) {
    http_response_code(400);
    echo json_encode(['error' => 'Passwords do not match']);
    exit;
}

// Validate token
$reset = DB::getPasswordReset($token);
if (!$reset) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid or expired token']);
    exit;
}

// Find user by email
$stmt = $db->prepare("SELECT id FROM users WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $reset['email']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$user) {
    http_response_code(400);
    echo json_encode(['error' => 'User not found']);
    exit;
}

// Update password
$hashed = password_hash($password, PASSWORD_DEFAULT);
$upd = $db->prepare("UPDATE users SET password = :pass WHERE id = :id");
$ok = $upd->execute(['pass' => $hashed, 'id' => $user['id']]);
if (!$ok) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to update password']);
    exit;
}

DB::consumePasswordReset($token);

if (method_exists('DB', 'logActivity')) {
    DB::logActivity($user['id'], 'password_reset_completed', 'user', $user['id'], null, $_SERVER['REMOTE_ADDR'] ?? null);
}

echo json_encode(['success' => true, 'message' => 'Password updated successfully']);

?>
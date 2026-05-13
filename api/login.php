<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';        // ← ADD
require_once __DIR__ . '/../functions/auth.php'; 
require_once __DIR__ . '/../functions/helpers.php';
header('Content-Type: application/json');

$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    http_response_code(400);
    echo json_encode(['error' => 'Missing email or password']);
    exit;
}

// Validate credentials
$stmt = $db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user || !password_verify($password, $user['password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

// Generate JWT
$token = Auth::generateJwt($user, 3600);

echo json_encode(['success' => true, 'token' => $token, 'user' => ['id' => $user['id'], 'username' => $user['username'], 'role' => $user['role']]]);

?>
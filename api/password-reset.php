<?php
<<<<<<< HEAD

=======
>>>>>>> 183087cbeab0abd8496df8aa0ca913725a72bdca
use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../libs/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/../libs/PHPMailer/src/SMTP.php';
require_once __DIR__ . '/../libs/PHPMailer/src/Exception.php';

header('Content-Type: application/json');

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

$stmt = $db->prepare("SELECT id, email, username FROM users WHERE email = :email LIMIT 1");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['success' => true, 'message' => 'If an account exists, a reset email has been sent']);
    exit;
}

$token = DB::createPasswordReset($email, 3600);
if (!$token) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to create reset token']);
    exit;
}

$resetLink = rtrim(SITE_URL, '/') . '/pages/password-reset.php?token=' . urlencode($token);
<<<<<<< HEAD
try {
    $mail = new PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $_ENV['MAIL_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['MAIL_USER'];
    $mail->Password   = $_ENV['MAIL_PASS'];
    $mail->SMTPSecure = 'tls';
    $mail->Port       = $_ENV['MAIL_PORT'];
    $mail->setFrom($_ENV['MAIL_FROM'], SITE_NAME);
    $mail->addAddress($email, $user['username']);
    $mail->Subject = 'Password Reset Request';
    $mail->Body    = "Hello {$user['username']},\n\nReset link (valid 1 hour):\n\n{$resetLink}\n\nIgnore if you didn't request this.";
    $mail->send();
} catch (Exception $e) {
    error_log('Mailer Error: ' . $e->getMessage());
}
=======

$mail = new PHPMailer(true);
$mail->isSMTP();
$mail->Host       = $_ENV['MAIL_HOST'];
$mail->SMTPAuth   = true;
$mail->Username   = $_ENV['MAIL_USER'];
$mail->Password   = $_ENV['MAIL_PASS'];
$mail->SMTPSecure = 'tls';
$mail->Port       = $_ENV['MAIL_PORT'];
$mail->setFrom($_ENV['MAIL_FROM'], SITE_NAME);
$mail->addAddress($email, $user['username']);
$mail->Subject = 'Password Reset Request';
$mail->Body    = "Hello {$user['username']},\n\nReset link (valid 1 hour):\n\n{$resetLink}\n\nIgnore if you didn't request this.";
$mail->send();

>>>>>>> 183087cbeab0abd8496df8aa0ca913725a72bdca
if (method_exists('DB', 'logActivity')) {
    DB::logActivity($user['id'], 'password_reset_requested', 'user', $user['id'], null, $_SERVER['REMOTE_ADDR'] ?? null);
}

<<<<<<< HEAD
echo json_encode(['success' => true, 'message' => 'If an account exists, a reset email has been sent']);
=======
echo json_encode(['success' => true, 'message' => 'If an account exists, a reset email has been sent']);
>>>>>>> 183087cbeab0abd8496df8aa0ca913725a72bdca

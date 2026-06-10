<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/db.php';

// Must be logged in
Auth::requireLogin();

global $db;
$error   = '';
$success = '';

// Fetch own data
$stmt = $db->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    Auth::logout();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ── Update username / email ──────────────────────────
    if ($action === 'update_profile') {
        $newUsername = trim($_POST['username'] ?? '');
        $newEmail    = trim($_POST['email']    ?? '');

        if (empty($newUsername) || empty($newEmail)) {
            $error = 'Username and email cannot be empty.';
        } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
            $error = 'Invalid email address.';
        } else {
            $stmt = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$newUsername, $newEmail, $user['id']]);
            if ($stmt->fetch()) {
                $error = 'Username or email already taken.';
            } else {
                $stmt = $db->prepare("UPDATE users SET username = ?, email = ? WHERE id = ?");
                if ($stmt->execute([$newUsername, $newEmail, $user['id']])) {
                    $_SESSION['username'] = $newUsername;
                    $user['username']     = $newUsername;
                    $user['email']        = $newEmail;
                    $success = 'Profile updated successfully.';
                } else {
                    $error = 'Database error — could not update profile.';
                }
            }
        }
    }

    // ── Change password ──────────────────────────────────
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword     = $_POST['new_password']     ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required.';
        } elseif (strlen($newPassword) < 8) {
            $error = 'New password must be at least 8 characters.';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match.';
        } else {
            $stmt = $db->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->execute([$user['id']]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if (!$row || !password_verify($currentPassword, $row['password'])) {
                $error = 'Current password is incorrect.';
            } else {
                $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                $stmt   = $db->prepare("UPDATE users SET password = ? WHERE id = ?");
                if ($stmt->execute([$hashed, $user['id']])) {
                    $success = 'Password changed successfully.';
                } else {
                    $error = 'Database error — could not change password.';
                }
            }
        }
    }
}

$pageTitle = 'My Profile';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div class="auth-form" style="max-width:560px;">
            <h1>My Profile</h1>
            <p style="color:#666;margin-bottom:24px;">Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong></p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <!-- Account Info -->
            <h2 style="font-size:1.1rem;margin-bottom:16px;border-bottom:1px solid #ccc;padding-bottom:8px;">Account Information</h2>
            <form method="POST">
                <input type="hidden" name="action" value="update_profile">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username"
                           value="<?php echo htmlspecialchars($user['username']); ?>"
                           required maxlength="50">
                </div>
                <div class="form-group">
                    <label for="email">Email Address</label>
                    <input type="email" id="email" name="email"
                           value="<?php echo htmlspecialchars($user['email']); ?>"
                           required maxlength="255">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Save Changes</button>
                </div>
            </form>

            <br>

            <!-- Change Password -->
            <h2 style="font-size:1.1rem;margin-bottom:16px;border-bottom:1px solid #ccc;padding-bottom:8px;">Change Password</h2>
            <form method="POST">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                           required autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label for="new_password">New Password <small style="color:#888;">(min 8 chars)</small></label>
                    <input type="password" id="new_password" name="new_password"
                           required minlength="8" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           required minlength="8" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Change Password</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
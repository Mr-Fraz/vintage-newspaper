<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/db.php';

Auth::requireLogin();

global $db;
$error   = '';
$success = '';

$stmt = $db->prepare("SELECT id, username, email FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) { Auth::logout(); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newUsername     = trim($_POST['username']         ?? '');
    $newEmail        = trim($_POST['email']            ?? '');
    $currentPassword = $_POST['current_password']      ?? '';
    $newPassword     = $_POST['new_password']          ?? '';
    $confirmPassword = $_POST['confirm_password']      ?? '';

    // Validate basic fields
    if (empty($newUsername) || empty($newEmail)) {
        $error = 'Username and email cannot be empty.';
    } elseif (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        // Check uniqueness
        $stmt = $db->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->execute([$newUsername, $newEmail, $user['id']]);
        if ($stmt->fetch()) {
            $error = 'Username or email already taken.';
        } else {
            $passwordChanged = false;

            // If any password field filled — validate & change
            if (!empty($currentPassword) || !empty($newPassword) || !empty($confirmPassword)) {
                if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                    $error = 'Fill all three password fields to change password.';
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
                        $passwordChanged = true;
                    }
                }
            }

            if (empty($error)) {
                if ($passwordChanged) {
                    $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
                    $stmt = $db->prepare("UPDATE users SET username=?, email=?, password=? WHERE id=?");
                    $ok = $stmt->execute([$newUsername, $newEmail, $hashed, $user['id']]);
                } else {
                    $stmt = $db->prepare("UPDATE users SET username=?, email=? WHERE id=?");
                    $ok = $stmt->execute([$newUsername, $newEmail, $user['id']]);
                }

                if ($ok) {
                    $_SESSION['username'] = $newUsername;
                    $user['username'] = $newUsername;
                    $user['email']    = $newEmail;
                    $success = 'Changes saved successfully.' . ($passwordChanged ? ' Password updated.' : '');
                } else {
                    $error = 'Database error — could not save changes.';
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
            <p style="color:#888;margin-bottom:24px;font-size:0.85rem;">
                Logged in as <strong><?php echo htmlspecialchars($_SESSION['username']); ?></strong>
                &nbsp;·&nbsp; Leave password fields blank to keep current password.
            </p>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST">
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

                <hr style="border:none;border-top:1px solid #ccc;margin:20px 0;">
                <p style="font-size:0.8rem;color:#888;margin-bottom:14px;">
                    Change Password <em>(optional — leave blank to keep current)</em>
                </p>

                <div class="form-group">
                    <label for="current_password">Current Password</label>
                    <input type="password" id="current_password" name="current_password"
                           autocomplete="current-password">
                </div>
                <div class="form-group">
                    <label for="new_password">New Password <small style="color:#aaa;">(min 8 chars)</small></label>
                    <input type="password" id="new_password" name="new_password"
                           minlength="8" autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password"
                           minlength="8" autocomplete="new-password">
                </div>

                <div class="form-group" style="margin-top:24px;">
                    <button type="submit" class="btn btn-primary" style="width:100%;">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/db.php';

$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

// Get user details
global $db;
$stmt = $db->prepare("SELECT id, username, email, role, created_at FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die('User not found');
}

// Check if trying to demote self
$canChangeRole = true;
if ($_SESSION['user_id'] == $id && $_SESSION['role'] == 'admin') {
    // Admin cannot demote themselves
    $canChangeRole = false;
}

$pageTitle = 'Manage User';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Manage User</h1>
        </header>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo Helper::escape($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo Helper::escape($success); ?></div>
        <?php endif; ?>

        <div class="user-details" style="background: #f9f9f9; padding: 20px; border-radius: 4px; margin-bottom: 30px;">
            <h2><?php echo Helper::escape($user['username']); ?></h2>
            <p><strong>Email:</strong> <?php echo Helper::escape($user['email']); ?></p>
            <p><strong>Current Role:</strong> <span class="badge badge-<?php echo $user['role']; ?>"><?php echo $user['role']; ?></span></p>
            <p><strong>Registered:</strong> <?php echo date('M j, Y \a\t h:i A', strtotime($user['created_at'])); ?></p>
        </div>

        <?php if ($canChangeRole): ?>
            <form method="POST" action="edit.php" class="admin-form">
                <input type="hidden" name="id" value="<?php echo (int)$user['id']; ?>">
                
                <div class="form-group">
                    <label for="role">User Role *</label>
                    <select id="role" name="role" required>
                        <option value="user" <?php echo $user['role'] === 'user' ? 'selected' : ''; ?>>User</option>
                        <option value="admin" <?php echo $user['role'] === 'admin' ? 'selected' : ''; ?>>Administrator</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Role</button>
                    <a href="list.php" class="btn btn-secondary">Back to Users</a>
                </div>
            </form>
        <?php else: ?>
            <div class="warning-message">
                <p><strong>Note:</strong> You cannot change your own role.</p>
            </div>
            <div class="form-actions">
                <a href="list.php" class="btn btn-secondary">Back to Users</a>
            </div>
        <?php endif; ?>

        <?php if ($id !== $_SESSION['user_id']): ?>
            <hr style="margin: 30px 0;">
            <div style="margin-top: 30px;">
                <h3>Danger Zone</h3>
                <a href="delete.php?id=<?php echo (int)$user['id']; ?>" class="btn btn-danger" onclick="return confirm('Delete this user? This action cannot be undone.')">Delete User</a>
            </div>
        <?php endif; ?>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>

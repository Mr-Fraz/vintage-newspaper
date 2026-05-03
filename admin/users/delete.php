<?php
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

// Prevent self-deletion
if ($id === $_SESSION['user_id']) {
    header('Location: list.php?error=Cannot delete your own account');
    exit;
}

// Check if user exists
global $db;
$stmt = $db->prepare("SELECT id, username FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header('Location: list.php?error=User not found');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!Helper::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } elseif (($_POST['confirm'] ?? '') !== 'yes') {
        $error = 'Please confirm deletion.';
    } else {
        $stmt = $db->prepare("DELETE FROM users WHERE id = ?");
        if ($stmt->execute([$id])) {
            header("Location: list.php?deleted=1");
            exit;
        } else {
            $error = 'Error: Could not delete user';
        }
    }
}

$pageTitle = 'Delete User';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Delete User</h1>
        </header>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo Helper::escape($error); ?></div>
        <?php endif; ?>

        <div class="warning-message" style="background: #fee; padding: 15px; border-left: 4px solid #c33; margin-bottom: 20px;">
            <p>Are you sure you want to delete the user: <strong><?php echo Helper::escape($user['username']); ?></strong>?</p>
            <p style="color: #c33;"><strong>Warning:</strong> This action cannot be undone.</p>
        </div>

        <form method="POST" class="admin-form">
            <?php echo Helper::csrfField(); ?>
            
            <div class="form-group">
                <label>
                    <input type="checkbox" name="confirm" value="yes" required> 
                    Yes, I confirm deletion of this user
                </label>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Delete User</button>
                <a href="manage.php?id=<?php echo (int)$user['id']; ?>" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>

<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';

$error = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid user ID');
}

$id = (int)$_GET['id'];

// Prevent self-deletion
if ($id === $_SESSION['user']['id']) {
    die('You cannot delete your own account');
}

// Check if user exists
$stmt = $conn->prepare("SELECT id, name FROM users WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    die('User not found');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } elseif (($_POST['confirm'] ?? '') !== 'yes') {
        $error = 'Please confirm deletion.';
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            header("Location: list.php?deleted=1");
            exit;
        } else {
            $error = 'Error: ' . $stmt->error;
        }
        $stmt->close();
    }
}
?>

<h2>Delete User</h2>

<?php if (!empty($error)): ?>
    <div class="error-message"><?php echo escape($error); ?></div>
<?php endif; ?>

<div class="warning-message">
    <p>Are you sure you want to delete the user: <strong><?php echo escape($user['name']); ?></strong>?</p>
    <p>This action cannot be undone.</p>
</div>

<form method="POST">
    <?php echo csrfField(); ?>
    
    <label>
        <input type="checkbox" name="confirm" value="yes" required> 
        Yes, I confirm deletion
    </label><br><br>
    
    <button type="submit" class="delete-btn">Delete User</button>
    <a href="list.php">Cancel</a>
</form>

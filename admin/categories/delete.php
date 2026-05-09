<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';

$error = '';

// Use the PDO connection from config
global $db;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

// Check if category exists (PDO)
$stmt = $db->prepare("SELECT id FROM categories WHERE id = ?");
$stmt->execute([$id]);
if ($stmt->rowCount() === 0) {
    header('Location: list.php');
    exit;
}

// Check if category has articles (PDO)
$stmt = $db->prepare("SELECT COUNT(*) FROM articles WHERE category_id = ?");
$stmt->execute([$id]);
$articleCount = (int)$stmt->fetchColumn();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } elseif (($_POST['confirm'] ?? '') !== 'yes') {
        $error = 'Please confirm deletion.';
    } else {
        $stmt = $db->prepare("DELETE FROM categories WHERE id = ?");
        if ($stmt->execute([$id])) {
            header("Location: list.php?deleted=1");
            exit;
        } else {
            $error = 'Error deleting category';
        }
    }
}
?>

<?php require_once __DIR__ . '/../includes/admin-header.php'; ?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Delete Category</h1>
        </header>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo escape($error); ?></div>
        <?php endif; ?>

        <div class="warning-message">
            <p>Are you sure you want to delete this category?</p>
            <?php if ($articleCount > 0): ?>
                <p><strong>Warning:</strong> This category has <?php echo (int)$articleCount; ?> article(s) associated with it. Deleting this category will set those articles to no category.</p>
            <?php endif; ?>
            <p>This action cannot be undone.</p>
        </div>

        <form method="POST">
            <?php echo csrfField(); ?>

            <label>
                <input type="checkbox" name="confirm" value="yes" required>
                Yes, I confirm deletion
            </label><br><br>

            <div class="form-actions">
                <button type="submit" class="btn btn-danger">Delete Category</button>
                <a href="list.php" class="btn">Cancel</a>
            </div>
        </form>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>

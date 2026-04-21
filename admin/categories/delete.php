<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');
require('../../functions/helpers.php');

$error = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid category ID');
}

$id = (int)$_GET['id'];

// Check if category exists
$stmt = $conn->prepare("SELECT id FROM categories WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
if ($stmt->get_result()->num_rows === 0) {
    $stmt->close();
    die('Category not found');
}
$stmt->close();

// Check if category has articles
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM articles WHERE category_id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$articleCount = $stmt->get_result()->fetch_assoc()['count'];
$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } elseif (($_POST['confirm'] ?? '') !== 'yes') {
        $error = 'Please confirm deletion.';
    } else {
        $stmt = $conn->prepare("DELETE FROM categories WHERE id = ?");
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

<h2>Delete Category</h2>

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
    
    <button type="submit" class="delete-btn">Delete Category</button>
    <a href="list.php">Cancel</a>
</form>

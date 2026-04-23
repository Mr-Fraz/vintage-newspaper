<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';

$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid category ID');
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT id, name, slug, description FROM categories WHERE id = ?");
if (!$stmt) die('Database error');
$stmt->bind_param("i", $id);
$stmt->execute();
$category = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$category) die('Category not found');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } else {
        $errors = validateRequired($_POST, ['name', 'slug', 'description']);
        
        if (empty($errors)) {
            $name = sanitizeInput($_POST['name']);
            $slug = sanitizeInput($_POST['slug']);
            $description = sanitizeInput($_POST['description']);
            
            if (strlen($name) < 2) {
                $error = 'Name must be at least 2 characters.';
            } elseif (strlen($slug) < 2 || !preg_match('/^[a-z0-9-]+$/', $slug)) {
                $error = 'Slug must be lowercase letters, numbers, and hyphens only.';
            } elseif (strlen($description) < 5) {
                $error = 'Description must be at least 5 characters.';
            } else {
                // Check if slug is unique (excluding current category)
                $stmt = $conn->prepare("SELECT id FROM categories WHERE slug = ? AND id != ?");
                $stmt->bind_param("si", $slug, $id);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $error = 'Slug already exists.';
                    $stmt->close();
                } else {
                    $stmt->close();
                    
                    $stmt = $conn->prepare("UPDATE categories SET name = ?, slug = ?, description = ? WHERE id = ?");
                    $stmt->bind_param("sssi", $name, $slug, $description, $id);
                    if ($stmt->execute()) {
                        $success = 'Category updated! Redirecting...';
                        header("refresh:2;url=list.php?updated=1");
                    } else {
                        $error = 'Error: ' . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        } else {
            $error = implode(' ', $errors);
        }
    }
}
?>

<h2>Edit Category</h2>

<?php if (!empty($error)): ?>
    <div class="error-message"><?php echo escape($error); ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success-message"><?php echo escape($success); ?></div>
<?php endif; ?>

<form method="POST">
    <?php echo csrfField(); ?>
    
    <label>Category Name *</label>
    <input type="text" name="name" value="<?php echo escape($category['name']); ?>" required><br>
    
    <label>Slug *</label>
    <input type="text" name="slug" value="<?php echo escape($category['slug']); ?>" required><br>
    
    <label>Description *</label>
    <textarea name="description" required><?php echo escape($category['description']); ?></textarea><br>
    
    <button type="submit">Update Category</button>
    <a href="list.php">Cancel</a>
</form>

<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';

$error = '';
$success = '';

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
                // Check if slug is unique
                $stmt = $conn->prepare("SELECT id FROM categories WHERE slug = ?");
                $stmt->bind_param("s", $slug);
                $stmt->execute();
                if ($stmt->get_result()->num_rows > 0) {
                    $error = 'Slug already exists.';
                    $stmt->close();
                } else {
                    $stmt->close();
                    
                    $stmt = $conn->prepare("INSERT INTO categories (name, slug, description) VALUES (?, ?, ?)");
                    $stmt->bind_param("sss", $name, $slug, $description);
                    if ($stmt->execute()) {
                        $success = 'Category added successfully! Redirecting...';
                        header("refresh:2;url=list.php");
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

<h2>Add New Category</h2>

<?php if (!empty($error)): ?>
    <div class="error-message"><?php echo escape($error); ?></div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success-message"><?php echo escape($success); ?></div>
<?php endif; ?>

<form method="POST">
    <?php echo csrfField(); ?>
    
    <label>Category Name *</label>
    <input type="text" name="name" placeholder="e.g., Breaking News" required><br>
    
    <label>Slug *</label>
    <input type="text" name="slug" placeholder="e.g., breaking-news" required><br>
    
    <label>Description *</label>
    <textarea name="description" placeholder="Brief description of this category" required></textarea><br>
    
    <button type="submit">Add Category</button>
    <a href="list.php">Cancel</a>
</form>

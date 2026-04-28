<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!Helper::verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } else {
        $name = trim(htmlspecialchars($_POST['name'], ENT_QUOTES, 'UTF-8'));
        $slug = trim(htmlspecialchars($_POST['slug'], ENT_QUOTES, 'UTF-8'));
        $description = trim(htmlspecialchars($_POST['description'], ENT_QUOTES, 'UTF-8'));
        
        if (strlen($name) < 2) {
            $error = 'Name must be at least 2 characters.';
        } elseif (!preg_match('/^[a-z0-9-]+$/', $slug)) {
            $error = 'Invalid slug format.';
        } elseif (strlen($description) < 5) {
            $error = 'Description too short.';
        } elseif (DB::slugExists($slug)) {
            $error = 'Slug already exists.';
        } else {
            $data = [
                'name' => $name,
                'slug' => $slug,
                'description' => $description
            ];
            
            if (DB::addCategory($data)) {
                $success = 'Category added successfully! Redirecting...';
                header("refresh:2;url=list.php");
            } else {
                $error = 'Add failed.';
            }
        }
    }
}

$pageTitle = 'Add New Category';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Add New Category</h1>
        </header>

        <?php if (!empty($error)): ?>
            <div class="error-message"><?php echo Helper::escape($error); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="success-message"><?php echo Helper::escape($success); ?></div>
        <?php endif; ?>

        <form method="POST" class="admin-form">
            <?php echo Helper::csrfField(); ?>
            
            <div class="form-group">
                <label for="name">Category Name *</label>
                <input type="text" id="name" name="name" placeholder="e.g., Breaking News" required>
            </div>
            
            <div class="form-group">
                <label for="slug">Slug *</label>
                <input type="text" id="slug" name="slug" placeholder="e.g., breaking-news" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" placeholder="Brief description of this category" required></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Add Category</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>

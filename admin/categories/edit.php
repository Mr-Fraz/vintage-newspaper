
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/helpers.php';
require_once __DIR__ . '/../../functions/db.php';

$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    //die('Invalid category ID');
    header('Location: list.php');
    exit;
}

$id = (int)$_GET['id'];

$category = DB::getCategoryById($id);
if (!$category) die('Category not found');
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
        } elseif (DB::slugExists($slug, $id)) {
            $error = 'Slug already exists.';
        } else {

            $data = [
                'id' => $id,
                'name' => $name,
                'slug' => $slug,
                'description' => $description
            ];

            if (DB::updateCategory($data)) {
                $success = 'Category updated! Redirecting...';
                header("refresh:2;url=list.php?updated=1");
            } else {
                $error = 'Update failed.';
            }
        }
    }
}

$pageTitle = 'Edit Category';
require_once __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Edit Category</h1>
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
                <input type="text" id="name" name="name" value="<?php echo Helper::escape($category['name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="slug">Slug *</label>
                <input type="text" id="slug" name="slug" value="<?php echo Helper::escape($category['slug']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required><?php echo Helper::escape($category['description']); ?></textarea>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Category</button>
                <a href="list.php" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </main>
</div>

<?php require_once __DIR__ . '/../includes/admin-footer.php'; ?>
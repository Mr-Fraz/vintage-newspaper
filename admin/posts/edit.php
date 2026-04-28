<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/db.php';
require_once __DIR__ . '/../../functions/validation.php';
require_once __DIR__ . '/../../functions/helpers.php';

$pageTitle = 'Edit Post';
$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = DB::getArticleForEdit($id);

if (!$article) {
    header('Location: list.php');
    exit;
}

$categories = DB::getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = Validate::sanitize($_POST['title']);
    $slug = Validate::slug($title);
    $content = $_POST['content'];
    $excerpt = Validate::sanitize($_POST['excerpt']);
    $category_id = (int)$_POST['category_id'];
    $status = Validate::sanitize($_POST['status']);
    
$imageName = $article['featured_image'];

if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $upload = Helper::uploadImage($_FILES['image']);

    if ($upload['success']) {
        $imageName = $upload['filename'];
    }
}
    
    $data = [
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'excerpt' => $excerpt,
        'featured_image' => $imageName,
        'category_id' => $category_id,
        'status' => $status
    ];
    
    if (DB::updateArticle($id, $data)) {
        $success = 'Article updated successfully!';
        header('Location: list.php');
        exit;
    } else {
        $error = 'Failed to update article';
    }
}

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Edit Post</h1>
        </header>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" class="admin-form">
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $article['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" rows="15" required><?php echo htmlspecialchars($article['content']); ?></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Featured Image</label>
                <?php if ($article['featured_image']): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo htmlspecialchars($article['featured_image']); ?>" alt="Current" style="max-width: 200px; margin-bottom: 10px;">
                <?php endif; ?>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <option value="draft" <?php echo $article['status'] == 'draft' ? 'selected' : ''; ?>>Draft</option>
                    <option value="published" <?php echo $article['status'] == 'published' ? 'selected' : ''; ?>>Published</option>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Update Post</button>
                <a href="list.php" class="btn">Cancel</a>
            </div>
        </form>
    </main>
</div>

</body>
</html>

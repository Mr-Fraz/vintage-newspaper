<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/db.php';
require_once __DIR__ . '/../../functions/validation.php';
require_once __DIR__ . '/../../functions/helpers.php';

$pageTitle = 'Add New Post';
$error = '';
$success = '';

$categories = DB::getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (empty($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token. Please try again.';
    }
    
    $title = Validate::sanitize($_POST['title']);
    $slug = Validate::slug($title);
    $content = $_POST['content']; // Keep HTML
    $excerpt = Validate::sanitize($_POST['excerpt']);
    $category_id = (int)$_POST['category_id'];
    $status = Validate::sanitize($_POST['status']);
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    $seo_title = isset($_POST['seo_title']) ? Validate::sanitize($_POST['seo_title']) : null;
    $meta_description = isset($_POST['meta_description']) ? Validate::sanitize($_POST['meta_description']) : null;
    $publish_at = isset($_POST['publish_at']) && $_POST['publish_at'] !== '' ? $_POST['publish_at'] : null;
    
    $imageName = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = Helper::uploadImage($_FILES['image']);
        if ($upload['success']) {
            $imageName = $upload['filename'];
        } else {
            $error = $upload['message'];
        }
    }
    
    if (empty($error)) {
        $data = [
            'title' => $title,
            'slug' => $slug,
            'content' => $content,
            'excerpt' => $excerpt,
            'image' => $imageName,
            'category_id' => $category_id,
            'author_id' => $_SESSION['user_id'],
            'status' => $status,
            'seo_title' => $seo_title,
            'meta_description' => $meta_description,
            'publish_at' => $publish_at,
            'og_image' => null
        ];

        $newId = DB::createArticle($data);
        if ($newId) {
            // Attach tags if provided (comma-separated)
            if (!empty($tags)) {
                $tagNames = array_filter(array_map('trim', explode(',', $tags)));
                DB::attachTagsToArticle($newId, $tagNames);
            }

            $success = 'Article created successfully!';
            header('Location: list.php');
            exit;
        } else {
            $error = 'Failed to create article';
        }
    }
}

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Add New Post</h1>
        </header>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" onsubmit="tinymce.triggerSave()">
            <?php echo csrfField(); ?>
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" required>
            </div>
            
            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3" placeholder="Short summary..."></textarea>
            </div>

            <div class="form-group">
                <label for="tags">Tags (comma-separated)</label>
                <input type="text" id="tags" name="tags" placeholder="e.g. politics, local, opinion" value="<?php echo isset($tags) ? htmlspecialchars($tags, ENT_QUOTES) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="seo_title">SEO Title</label>
                <input type="text" id="seo_title" name="seo_title" placeholder="Optional SEO title">
            </div>

            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="2" placeholder="Optional meta description"></textarea>
            </div>

            <div class="form-group">
                <label for="publish_at">Publish At (optional)</label>
                <input type="datetime-local" id="publish_at" name="publish_at">
            </div>
            
            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" rows="15" required></textarea>
            </div>
            
            <div class="form-group">
                <label for="image">Featured Image</label>
                <input type="file" id="image" name="image" accept="image/*">
            </div>
            
            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php
                        $statuses = ['draft','pending','scheduled','published','archived'];
                        foreach ($statuses as $st) {
                            $sel = (isset($status) && $status == $st) ? 'selected' : '';
                            echo '<option value="' . $st . '" ' . $sel . '>' . ucfirst($st) . '</option>';
                        }
                    ?>
                </select>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Create Post</button>
                <a href="list.php" class="btn">Cancel</a>
            </div>
        </form>
    </main>
</div>
<script>
tinymce.init({
  selector: '#content',
  plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
  toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
  height: 500,
  menubar: true,
  skin: 'oxide',        // dark skin — fits vintage dark admin
  content_css: 'default',
  branding: false,
  promotion: false
});
</script>
</body>
</html>

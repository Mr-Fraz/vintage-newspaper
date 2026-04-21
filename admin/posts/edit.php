<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');
require('../../functions/helpers.php');

$error = '';
$success = '';

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid article ID');
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT id, title, content, category_id FROM articles WHERE id=?");
if (!$stmt) die('Database error');
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$article) die('Article not found');

// Fetch categories
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$categoryList = [];
while ($cat = $categories->fetch_assoc()) {
    $categoryList[] = $cat;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } else {
        $errors = validateRequired($_POST, ['title', 'content']);
        if (empty($errors)) {
            $title = sanitizeInput($_POST['title']);
            $content = sanitizeInput($_POST['content']);
            $categoryId = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
            
            if (strlen($title) < 3) {
                $error = 'Title must be at least 3 characters.';
            } elseif (strlen($content) < 10) {
                $error = 'Content must be at least 10 characters.';
            } else {
                $stmt = $conn->prepare("UPDATE articles SET title=?, content=?, category_id=? WHERE id=?");
                $stmt->bind_param("ssii", $title, $content, $categoryId, $id);
                if ($stmt->execute()) {
                    $success = 'Article updated! Redirecting...';
                    header("refresh:2;url=list.php");
                } else {
                    $error = 'Error: ' . $stmt->error;
                }
                $stmt->close();
            }
        } else {
            $error = implode(' ', $errors);
        }
    }
}
?>

<h2>Edit Article</h2>
<?php if (!empty($error)): ?>
    <div class="error-message"><?php echo escape($error); ?></div>
<?php endif; ?>
<?php if (!empty($success)): ?>
    <div class="success-message"><?php echo escape($success); ?></div>
<?php endif; ?>
<form method="POST">
    <?php echo csrfField(); ?>
    <label>Title *</label>
    <input type="text" name="title" value="<?php echo escape($article['title']); ?>" required><br>
    
    <label>Category</label>
    <select name="category_id">
        <option value="">-- Select Category --</option>
        <?php foreach ($categoryList as $cat): ?>
            <option value="<?php echo (int)$cat['id']; ?>" <?php echo $article['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                <?php echo escape($cat['name']); ?>
            </option>
        <?php endforeach; ?>
    </select><br>
    
    <label>Content *</label>
    <textarea name="content" required rows="10"><?php echo escape($article['content']); ?></textarea><br>
    <button type="submit">Update</button>
    <a href="list.php">Cancel</a>
</form>
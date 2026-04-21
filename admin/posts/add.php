<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');
require('../../functions/helpers.php');

$error = '';
$success = '';

// Get current user ID
$userId = $_SESSION['user']['id'];

// Fetch categories
$categories = $conn->query("SELECT id, name FROM categories ORDER BY name");
$categoryList = [];
while ($cat = $categories->fetch_assoc()) {
    $categoryList[] = $cat;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } else {
        // Validate inputs
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
                $stmt = $conn->prepare("INSERT INTO articles (title, content, category_id, author_id, status) VALUES (?, ?, ?, ?, ?)");
                if (!$stmt) {
                    $error = 'Database error: ' . $conn->error;
                } else {
                    $status = 'published';
                    $stmt->bind_param("ssisi", $title, $content, $categoryId, $userId, $status);
                    if ($stmt->execute()) {
                        $success = 'Article added successfully! Redirecting...';
                        header("refresh:2;url=list.php");
                    } else {
                        $error = 'Error adding article: ' . $stmt->error;
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

<h2>Add Article</h2>

<?php if (!empty($error)): ?>
    <div class="error-message">
        <?php echo escape($error); ?>
    </div>
<?php endif; ?>

<?php if (!empty($success)): ?>
    <div class="success-message">
        <?php echo escape($success); ?>
    </div>
<?php endif; ?>

<form method="POST">
    <?php echo csrfField(); ?>
    
    <label>Title *</label>
    <input type="text" name="title" placeholder="Article Title" required><br>
    
    <label>Category</label>
    <select name="category_id">
        <option value="">-- Select Category --</option>
        <?php foreach ($categoryList as $cat): ?>
            <option value="<?php echo (int)$cat['id']; ?>"><?php echo escape($cat['name']); ?></option>
        <?php endforeach; ?>
    </select><br>
    
    <label>Content *</label>
    <textarea name="content" placeholder="Article Content" required rows="10"></textarea><br>
    
    <button type="submit">Add Article</button>
    <a href="list.php">Cancel</a>
</form>
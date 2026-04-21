<?php
require('../../includes/init.php');
require('../../includes/auth-middleware.php');
require('../../functions/helpers.php');

$error = '';
$success = '';

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
            
            if (strlen($title) < 3) {
                $error = 'Title must be at least 3 characters.';
            } else if (strlen($content) < 10) {
                $error = 'Content must be at least 10 characters.';
            } else {
                $stmt = $conn->prepare("INSERT INTO articles (title, content) VALUES (?,?)");
                if (!$stmt) {
                    $error = 'Database error: ' . $conn->error;
                } else {
                    $stmt->bind_param("ss", $title, $content);
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
    
    <input type="text" name="title" placeholder="Title" required><br>
    <textarea name="content" placeholder="Content" required></textarea><br>
    <button type="submit">Add</button>
</form>
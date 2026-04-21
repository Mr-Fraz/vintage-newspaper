<?php
require('../includes/init.php');
require('../functions/auth.php');

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } else {
        // Validate inputs
        $errors = validateRequired($_POST, ['email', 'password']);
        
        if (empty($errors)) {
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            
            if (!validateEmail($email)) {
                $error = 'Invalid email format.';
            } else {
                $user = loginUser($conn, $email, $password);
                
                if ($user) {
                    $_SESSION['user'] = $user;
                    
                    if ($user['role'] == 'admin') {
                        header("Location: ../admin/index.php");
                    } else {
                        header("Location: ../index.php");
                    }
                    exit;
                } else {
                    $error = 'Invalid email or password.';
                }
            }
        } else {
            $error = implode(' ', $errors);
        }
    }
}
?>

<h2>Login</h2>

<?php if (!empty($error)): ?>
    <div class="error-message">
        <?php echo escape($error); ?>
    </div>
<?php endif; ?>

<form method="POST">
    <?php echo csrfField(); ?>
    
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit">Login</button>
</form> 

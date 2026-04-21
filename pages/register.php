<?php
require('../includes/init.php');
require('../functions/auth.php');

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Verify CSRF token
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Security error. Please try again.';
    } else {
        // Validate inputs
        $errors = validateRequired($_POST, ['name', 'email', 'password']);
        
        if (empty($errors)) {
            $name = sanitizeInput($_POST['name']);
            $email = sanitizeInput($_POST['email']);
            $password = $_POST['password'];
            
            if (!validateEmail($email)) {
                $error = 'Invalid email format.';
            } else if (strlen($password) < 6) {
                $error = 'Password must be at least 6 characters.';
            } else {
                try {
                    registerUser($conn, $name, $email, $password);
                    $success = 'Registration successful! Redirecting to login...';
                    header("refresh:2;url=login.php");
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }
            }
        } else {
            $error = implode(' ', $errors);
        }
    }
}
?>

<h2>Register</h2>

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
    
    <input type="text" name="name" placeholder="Name" required><br>
    <input type="email" name="email" placeholder="Email" required><br>
    <input type="password" name="password" placeholder="Password (min 6 chars)" required><br>
    <button type="submit">Register</button>
</form> 

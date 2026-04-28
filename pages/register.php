<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/auth.php';
require_once __DIR__ . '/../functions/validation.php';

if (Auth::isLoggedIn()) {
    header('Location: ' . SITE_URL . '/admin/');
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = Validate::sanitize($_POST['username']);
    $email = Validate::sanitize($_POST['email']);
    $password = $_POST['password'];
    $confirmPassword = $_POST['confirm_password'];
    
    if ($password !== $confirmPassword) {
        $error = 'Passwords do not match';
    } else {
        $result = Auth::register($username, $email, $password);
        
        if ($result['success']) {
            $success = $result['message'] . ' You can now <a href="login.php">login</a>.';
        } else {
            $error = $result['message'];
        }
    }
}

$pageTitle = 'Register';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div class="auth-form">
            <h1>Register</h1>
            
            <?php if ($error): ?>
                <div class="alert alert-error"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" required minlength="3">
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required minlength="6">
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Register</button>
            </form>
            
            <p class="auth-footer">
                Already have an account? <a href="login.php">Login here</a>
            </p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

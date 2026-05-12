<?php
$pageTitle = 'Forgot Password';
require_once __DIR__ . '/../config/config.php';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div class="auth-form">
            <h1>Forgot Password</h1>
            <p>Enter your email and we'll send a reset link valid for 1 hour.</p>

            <form id="forgotForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-primary">Send Reset Link</button>
            </form>

            <p class="auth-footer"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</main>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('forgotForm');
    
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        btn.disabled = true;
        btn.textContent = 'Sending...';

        try {
            const res  = await fetch('<?= SITE_URL ?>/api/password-reset.php', {
                method: 'POST',
                body: new FormData(this)
            });
            const json = await res.json();

            // Always create fresh alert above form
            let msg = document.getElementById('forgot-msg');
            if (!msg) {
                msg = document.createElement('div');
                msg.id = 'forgot-msg';
                msg.className = 'alert';
                form.parentNode.insertBefore(msg, form);
            }

            msg.className = 'alert ' + (json.success ? 'alert-success' : 'alert-error');
            msg.textContent = json.success ? json.message : (json.error || 'Something went wrong');
            msg.style.display = 'block';

            if (json.success) form.style.display = 'none';
            else {
                btn.disabled = false;
                btn.textContent = 'Send Reset Link';
            }
        } catch (err) {
            btn.disabled = false;
            btn.textContent = 'Send Reset Link';
            alert('Request failed: ' + err.message);
        }
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
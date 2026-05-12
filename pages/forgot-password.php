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

            <div id="msg" class="alert" style="display:none"></div>

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
    document.getElementById('forgotForm').addEventListener('submit', async function(e) {
        e.preventDefault();
        const btn = this.querySelector('button');
        btn.disabled = true;
        btn.textContent = 'Sending...';

        const data = new FormData(this);
        const res = await fetch('<?= SITE_URL ?>/api/password-reset.php', {
            method: 'POST',
            body: data
        });
        const json = await res.json();

        const msg = document.getElementById('msg');
        msg.style.display = 'block';
        msg.className = 'alert ' + (json.success ? 'alert-success' : 'alert-error');
        msg.textContent = json.message || json.error;

        if (json.success) this.style.display = 'none';
        else {
            btn.disabled = false;
            btn.textContent = 'Send Reset Link';
        }
    });
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
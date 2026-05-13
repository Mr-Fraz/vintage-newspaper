<?php
$pageTitle = 'Reset Password';
require_once __DIR__ . '/../config/config.php';
$token = htmlspecialchars($_GET['token'] ?? '');
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div class="auth-form">
            <h1>Reset Password</h1>

            <?php if (!$token): ?>
                <div class="alert alert-error">Invalid or missing token.</div>
            <?php else: ?>
<<<<<<< HEAD
=======

                <div id="msg" class="alert" style="display:none"></div>

>>>>>>> 183087cbeab0abd8496df8aa0ca913725a72bdca
                <form id="resetForm">
                    <input type="hidden" name="token" value="<?= $token ?>">
                    <div class="form-group">
                        <label for="password">New Password</label>
                        <input type="password" id="password" name="password" minlength="8" required>
                    </div>
                    <div class="form-group">
                        <label for="confirm">Confirm Password</label>
                        <input type="password" id="confirm" name="confirm" minlength="8" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Reset Password</button>
                </form>

                <script>
<<<<<<< HEAD
                    document.addEventListener('DOMContentLoaded', function() {
                        const form = document.getElementById('resetForm');
                        if (!form) return;

                        form.addEventListener('submit', async function(e) {
                            e.preventDefault();
                            const btn = this.querySelector('button');
                            btn.disabled = true;
                            btn.textContent = 'Resetting...';

                            try {
                                const res = await fetch('<?= SITE_URL ?>/api/password-reset-verify.php', {
                                    method: 'POST',
                                    body: new FormData(this)
                                });
                                const json = await res.json();

                                let msg = document.getElementById('reset-msg');
                                if (!msg) {
                                    msg = document.createElement('div');
                                    msg.id = 'reset-msg';
                                    form.parentNode.insertBefore(msg, form);
                                }

                                msg.className = 'alert ' + (json.success ? 'alert-success' : 'alert-error');
                                msg.textContent = json.success ? json.message : (json.error || 'Something went wrong');
                                msg.style.display = 'block';

                                if (json.success) {
                                    form.style.display = 'none';
                                    setTimeout(() => window.location = '<?= SITE_URL ?>/pages/login.php', 2000);
                                } else {
                                    btn.disabled = false;
                                    btn.textContent = 'Reset Password';
                                }
                            } catch (err) {
                                btn.disabled = false;
                                btn.textContent = 'Reset Password';
                                alert('Request failed: ' + err.message);
                            }
                        });
=======
                    document.getElementById('resetForm').addEventListener('submit', async function(e) {
                        e.preventDefault();
                        const btn = this.querySelector('button');
                        btn.disabled = true;

                        const data = new FormData(this);
                        const res = await fetch('<?= SITE_URL ?>/api/password-reset-verify.php', {
                            method: 'POST',
                            body: data
                        });
                        const json = await res.json();

                        const msg = document.getElementById('msg');
                        msg.style.display = 'block';
                        msg.className = 'alert ' + (json.success ? 'alert-success' : 'alert-error');
                        msg.textContent = json.message || json.error;

                        if (json.success) {
                            this.style.display = 'none';
                            setTimeout(() => window.location = '<?= SITE_URL ?>/pages/login.php', 2000);
                        } else {
                            btn.disabled = false;
                        }
>>>>>>> 183087cbeab0abd8496df8aa0ca913725a72bdca
                    });
                </script>

            <?php endif; ?>

            <p class="auth-footer"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
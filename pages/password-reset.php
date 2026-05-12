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

                <div id="msg" class="alert" style="display:none"></div>

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
                    });
                </script>

            <?php endif; ?>

            <p class="auth-footer"><a href="login.php">Back to Login</a></p>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../functions/helpers.php';
?>
<nav>
    <a href="/index.php">Home</a>

    <?php if (isset($_SESSION['user'])): ?>
        <span>Welcome, <?php echo escape($_SESSION['user']['name']); ?></span>

        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="/admin/index.php">Admin Dashboard</a>
        <?php endif; ?>
        <a href="/admin/logout.php">Logout</a>
    <?php else: ?>
        <a href="/pages/login.php">Login</a>
        <a href="/pages/register.php">Register</a>
    <?php endif; ?>
    
    <input type="text" id="search" onkeyup="searchArticles()" placeholder="Search...">
    <div id="results"></div>
</nav>
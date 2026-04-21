<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<span>Welcome, <?php echo isset($_SESSION['user']['name']) ? htmlspecialchars($_SESSION['user']['name']) : 'User'; ?></span>
<nav>
    <a href="/index.php">Home</a>

    <?php if (isset($_SESSION['user'])): ?>
        <span>Welcome, <?php echo htmlspecialchars($_SESSION['user']['name']); ?></span>

        <?php if ($_SESSION['user']['role'] == 'admin'): ?>
            <a href="/admin/index.php">Admin</a>
        <?php endif; ?>
        <a href="logout.php">Logout</a>
        <a href="/admin/logout.php">Logout</a>
    <?php else: ?>
        <a href="/pages/login.php">Login</a>
        <a href="/pages/register.php">Register</a>
    <?php endif; ?>
    <!-- The searchArticles() JavaScript function should be defined in your scripts for search functionality -->
    <input type="text" id="search" onkeyup="searchArticles()" placeholder="Search...">
    <div id="results"></div>
</nav>
<aside class="admin-sidebar">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <span class="sidebar-title">Admin Panel</span>
            <span class="sidebar-user">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        <button class="admin-hamburger" id="adminHamburger" aria-label="Toggle menu">
            <span></span><span></span><span></span>
        </button>
    </div>

    <nav class="admin-nav" id="adminNav">
        <ul>
            <li><a href="<?php echo SITE_URL; ?>/admin/">📊 Dashboard</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/posts/list.php">📝 All Posts</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/posts/add.php">➕ Add New Post</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/categories/list.php">📁 Categories</a></li>
            <?php if (Auth::isAdmin()): ?>
            <li><a href="<?php echo SITE_URL; ?>/admin/users/list.php">👥 Users</a></li>
            <?php endif; ?>
            <li><a href="<?php echo SITE_URL; ?>/admin/comments/list.php">💬 Comments</a></li>
            <li><a href="<?php echo SITE_URL; ?>">🏠 View Site</a></li>
            <li><a href="<?php echo SITE_URL; ?>/pages/profile.php">⚙️ My Profile</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/logout.php">🚪 Logout</a></li>
        </ul>
    </nav>
</aside>

<script>
(function() {
    var btn = document.getElementById('adminHamburger');
    var nav = document.getElementById('adminNav');
    if (btn && nav) {
        btn.addEventListener('click', function() {
            nav.classList.toggle('open');
            btn.classList.toggle('active');
        });
    }
})();
</script>
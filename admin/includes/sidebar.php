<aside class="admin-sidebar">
    <div class="sidebar-header">
        <h2>Admin Panel</h2>
        <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></p>
    </div>
    
    <nav class="admin-nav">
        <ul>
            <li><a href="<?php echo SITE_URL; ?>/admin/">📊 Dashboard</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/posts/list.php">📝 All Posts</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/posts/add.php">➕ Add New Post</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/categories/list.php">📁 Categories</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/users/list.php">👥 Users</a></li>
            <li><a href="<?php echo SITE_URL; ?>">🏠 View Site</a></li>
            <li><a href="<?php echo SITE_URL; ?>/admin/logout.php">🚪 Logout</a></li>
        </ul>
    </nav>
</aside>

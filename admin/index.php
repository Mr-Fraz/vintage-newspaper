<?php
require('../includes/init.php');
require('../includes/auth-middleware.php');
require('../functions/helpers.php');

// Get statistics
$articlesResult = $conn->query("SELECT COUNT(*) as count FROM articles");
$articles = $articlesResult->fetch_assoc()['count'];

$usersResult = $conn->query("SELECT COUNT(*) as count FROM users");
$users = $usersResult->fetch_assoc()['count'];

$categoriesResult = $conn->query("SELECT COUNT(*) as count FROM categories");
$categories = $categoriesResult->fetch_assoc()['count'];
?>

<h2>Admin Dashboard</h2>

<div class="dashboard-stats">
    <div class="stat">
        <h3>Total Articles</h3>
        <p><?php echo (int)$articles; ?></p>
    </div>
    <div class="stat">
        <h3>Total Users</h3>
        <p><?php echo (int)$users; ?></p>
    </div>
    <div class="stat">
        <h3>Total Categories</h3>
        <p><?php echo (int)$categories; ?></p>
    </div>
</div>

<div class="quick-actions">
    <h3>Content Management</h3>
    <ul>
        <li><a href="posts/list.php">Manage Articles</a></li>
        <li><a href="posts/add.php">Add New Article</a></li>
        <li><a href="categories/list.php">Manage Categories</a></li>
        <li><a href="categories/add.php">Add New Category</a></li>
    </ul>
</div>

<div class="quick-actions">
    <h3>User Management</h3>
    <ul>
        <li><a href="users/list.php">Manage Users</a></li>
    </ul>
</div>
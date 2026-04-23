<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/db.php';
require_once __DIR__ . '/../../functions/helpers.php';

$pageTitle = 'All Posts';

global $db;
$articles = $db->query("SELECT a.*, u.username, c.name as category_name 
                        FROM articles a 
                        LEFT JOIN users u ON a.user_id = u.id 
                        LEFT JOIN categories c ON a.category_id = c.id 
                        ORDER BY a.created_at DESC")->fetchAll();

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>All Posts</h1>
            <a href="add.php" class="btn btn-primary">Add New Post</a>
        </header>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Author</th>
                    <th>Status</th>
                    <th>Views</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($articles as $article): ?>
                    <tr>
                        <td><?php echo $article['id']; ?></td>
                        <td><?php echo htmlspecialchars($article['title']); ?></td>
                        <td><?php echo htmlspecialchars($article['category_name']); ?></td>
                        <td><?php echo htmlspecialchars($article['username']); ?></td>
                        <td><span class="badge badge-<?php echo $article['status']; ?>"><?php echo $article['status']; ?></span></td>
                        <td><?php echo $article['views']; ?></td>
                        <td><?php echo Helper::formatDate($article['created_at'], 'M j, Y'); ?></td>
                        <td>
                            <a href="edit.php?id=<?php echo $article['id']; ?>" class="btn-sm">Edit</a>
                            <a href="delete.php?id=<?php echo $article['id']; ?>" class="btn-sm btn-danger" onclick="return confirm('Delete this article?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </main>
</div>

</body>
</html>

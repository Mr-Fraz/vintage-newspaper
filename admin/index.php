<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/includes/auth-check.php';
require_once __DIR__ . '/../functions/db.php';

$pageTitle = 'Dashboard';

// Get stats
global $db;
$statsQueries = [
    'total_articles' => "SELECT COUNT(*) as count FROM articles",
    'published_articles' => "SELECT COUNT(*) as count FROM articles WHERE status = 'published'",
    'total_users' => "SELECT COUNT(*) as count FROM users",
    'total_categories' => "SELECT COUNT(*) as count FROM categories"
];

$stats = [];
foreach ($statsQueries as $key => $query) {
    $result = $db->query($query);
    $stats[$key] = $result->fetch()['count'];
}

// Recent articles
$recentArticles = $db->query("SELECT a.*, u.username, c.name as category_name 
                                FROM articles a 
                    	        LEFT JOIN users u ON a.author_id = u.id 
                                LEFT JOIN categories c ON a.category_id = c.id 
                                ORDER BY a.created_at DESC LIMIT 5")->fetchAll();

include __DIR__ . '/includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/includes/sidebar.php'; ?>
    
    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Dashboard</h1>
        </header>
        
        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Articles</h3>
                <p class="stat-number"><?php echo $stats['total_articles']; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Published</h3>
                <p class="stat-number"><?php echo $stats['published_articles']; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Categories</h3>
                <p class="stat-number"><?php echo $stats['total_categories']; ?></p>
            </div>
            
            <div class="stat-card">
                <h3>Users</h3>
                <p class="stat-number"><?php echo $stats['total_users']; ?></p>
            </div>
        </div>
        
        <!-- Recent Articles -->
        <section class="admin-section">
            <h2>Recent Articles</h2>
            
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentArticles as $article): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($article['title']); ?></td>
                            <td><?php echo htmlspecialchars($article['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($article['username']); ?></td>
                            <td><span class="badge badge-<?php echo $article['status']; ?>"><?php echo $article['status']; ?></span></td>
                            <td><?php echo date('M j, Y', strtotime($article['created_at'])); ?></td>
                            <td>
                                <a href="<?php echo SITE_URL; ?>/admin/posts/edit.php?id=<?php echo $article['id']; ?>" class="btn-sm">Edit</a>
                                <a href="<?php echo SITE_URL; ?>/article/<?php echo $article['id']; ?>" class="btn-sm" target="_blank">View</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </main>
</div>

</body>
</html>

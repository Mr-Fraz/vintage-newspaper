<?php
require('../includes/init.php');
require('../functions/helpers.php');

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid article ID');
}

$id = (int)$_GET['id'];

$stmt = $conn->prepare("SELECT a.id, a.title, a.content, a.created_at, c.name as category_name, c.slug, u.name as author_name 
                        FROM articles a 
                        LEFT JOIN categories c ON a.category_id = c.id 
                        LEFT JOIN users u ON a.author_id = u.id 
                        WHERE a.id = ? AND a.status = 'published'");
if (!$stmt) die('Database error');
$stmt->bind_param("i", $id);
$stmt->execute();
$article = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$article) die('Article not found');

include('../includes/header.php');
?>

<div class="article-container">
    <?php if ($article['category_name']): ?>
        <a href="/pages/category.php?slug=<?php echo escape($article['slug']); ?>" class="category-link">
            <?php echo escape($article['category_name']); ?>
        </a>
    <?php endif; ?>
    
    <h1><?php echo escape($article['title']); ?></h1>
    
    <div class="article-meta">
        <small>
            <?php echo escape(date('F d, Y', strtotime($article['created_at']))); ?>
            <?php if ($article['author_name']): ?>
                by <?php echo escape($article['author_name']); ?>
            <?php endif; ?>
        </small>
    </div>
    
    <div class="article-content">
        <?php echo nl2br(escape($article['content'])); ?>
    </div>
</div>

<?php include('../includes/footer.php'); ?>
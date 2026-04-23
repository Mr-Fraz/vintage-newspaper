<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/validation.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$article = DB::getArticle($id);

if (!$article) {
    header('Location: ' . SITE_URL);
    exit;
}

$pageTitle = $article['title'];

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <article class="article-single">
            <header class="article-header">
                <span class="category-badge"><?php echo $article['category_name']; ?></span>
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                
                <div class="article-meta">
                    <span class="author">By <?php echo htmlspecialchars($article['author']); ?></span>
                    <span class="date"><?php echo Helper::formatDate($article['created_at'], 'F j, Y g:i A'); ?></span>
                </div>
            </header>
            
            <?php if ($article['image']): ?>
                <div class="article-image">
                    <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                </div>
            <?php endif; ?>
            
            <div class="article-content">
                <?php echo $article['content']; ?>
            </div>
            
            <footer class="article-footer">
                <a href="<?php echo SITE_URL; ?>" class="btn">← Back to Home</a>
                <a href="<?php echo SITE_URL; ?>/category/<?php echo $article['category_name']; ?>" class="btn">More in <?php echo $article['category_name']; ?></a>
            </footer>
        </article>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/validation.php';

$slug = isset($_GET['slug']) ? Validate::sanitize($_GET['slug']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$articles = DB::getArticlesByCategory($slug, $page);

if (empty($articles)) {
    header('Location: ' . SITE_URL);
    exit;
}

$categoryName = $articles[0]['category_name'];
$pageTitle = $categoryName;

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <header class="page-header">
            <h1>Category: <?php echo htmlspecialchars($categoryName); ?></h1>
        </header>
        
        <section class="articles-grid">
            <div class="grid">
                <?php foreach ($articles as $article): ?>
                    <article class="article-card">
                        <?php if ($article['image']): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <?php endif; ?>
                        
                        <div class="article-content">
                            <h3><a href="<?php echo SITE_URL; ?>/article/<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                            <p><?php echo Helper::excerpt($article['content'], 120); ?></p>
                            <div class="meta">
                                <span><?php echo htmlspecialchars($article['author']); ?></span>
                                <span><?php echo Helper::timeAgo($article['created_at']); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

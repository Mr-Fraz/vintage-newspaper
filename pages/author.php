<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';

$authorId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$author = DB::getAuthor($authorId);

if (!$author) {
    header('Location: ' . SITE_URL);
    exit;
}

$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$articles = DB::getArticlesByAuthor($authorId, $page);
$totalArticles = DB::countArticlesByAuthor($authorId);

$pageTitle = $author['username'];
$metaDescription = !empty($author['bio']) ? mb_substr(strip_tags($author['bio']), 0, 155) : '';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">

        <section class="author-header">
            <?php if (!empty($author['avatar'])): ?>
                <img class="author-avatar" src="<?php echo SITE_URL . $author['avatar']; ?>" alt="<?php echo htmlspecialchars($author['username']); ?>">
            <?php else: ?>
                <div class="author-avatar author-avatar-placeholder">
                    <i data-lucide="user-circle"></i>
                </div>
            <?php endif; ?>
            <div class="author-header-text">
                <span class="author-label">Correspondent</span>
                <h1><?php echo htmlspecialchars($author['username']); ?></h1>
                <?php if (!empty($author['title'])): ?>
                    <p class="author-title"><?php echo htmlspecialchars($author['title']); ?></p>
                <?php endif; ?>
                <?php if (!empty($author['bio'])): ?>
                    <p class="author-bio"><?php echo nl2br(htmlspecialchars($author['bio'])); ?></p>
                <?php endif; ?>
                <span class="author-since">Contributing since <?php echo Helper::formatDate($author['created_at'], 'F Y'); ?> &middot; <?php echo $totalArticles; ?> dispatches</span>
            </div>
        </section>

        <div class="col-section-head">Dispatches by <?php echo htmlspecialchars($author['username']); ?></div>

        <?php if (!empty($articles)): ?>
        <div class="grid">
            <?php foreach ($articles as $article): ?>
                <article class="article-card">
                    <?php if (!empty($article['image'])): ?>
                        <img src="<?php echo SITE_URL . $article['image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>" loading="lazy">
                    <?php endif; ?>
                    <div class="article-content">
                        <span class="category-badge"><?php echo htmlspecialchars($article['category_name'] ?? ''); ?></span>
                        <h3><a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                        <p><?php echo htmlspecialchars($article['excerpt'] ?? ''); ?></p>
                        <div class="meta">
                            <span><?php echo Helper::timeAgo($article['created_at']); ?></span>
                            <span>👁 <?php echo (int) $article['views']; ?></span>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        <?php echo Helper::pagination($totalArticles, POSTS_PER_PAGE, $page, SITE_URL . '/pages/author.php?id=' . $authorId); ?>
        <?php else: ?>
            <p class="no-comments">No published dispatches yet.</p>
        <?php endif; ?>

    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
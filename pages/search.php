<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/validation.php';

$query = isset($_GET['q']) ? Validate::sanitize($_GET['q']) : '';
$articles = [];

if (!empty($query)) {
    $articles = DB::searchArticles($query);
}

$pageTitle = 'Search Results';

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <header class="page-header">
            <h1>Search</h1>
            
            <form method="GET" action="" class="search-form">
                <input type="text" name="q" placeholder="Search articles..." value="<?php echo htmlspecialchars($query); ?>" required>
                <button type="submit" class="btn">Search</button>
            </form>
        </header>
        
        <?php if (!empty($query)): ?>
            <p class="search-info">
                <?php echo count($articles); ?> result(s) found for "<?php echo htmlspecialchars($query); ?>"
            </p>
            
            <?php if (count($articles) > 0): ?>
                <section class="articles-list">
                    <?php foreach ($articles as $article): ?>
                        <article class="article-item">
                            <div class="article-info">
                                <span class="category-badge"><?php echo $article['category_name']; ?></span>
                                <h3><a href="<?php echo SITE_URL; ?>/article/<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                                <p><?php echo Helper::excerpt($article['content'], 150); ?></p>
                                <div class="meta">
                                    <span><?php echo htmlspecialchars($article['author']); ?></span>
                                    <span><?php echo Helper::formatDate($article['created_at']); ?></span>
                                </div>
                            </div>
                            
                            <?php if ($article['image']): ?>
                                <div class="article-thumb">
                                    <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                </div>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </section>
            <?php else: ?>
                <div class="no-results">
                    <p>No articles found. Try different keywords.</p>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>

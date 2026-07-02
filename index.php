<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/db.php';
require_once __DIR__ . '/functions/helpers.php';

$pageTitle = 'Home';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$articles = DB::getArticles($page);
$totalArticles = DB::countArticles();

try {
    $breakingNews = DB::getBreakingNews(6);
} catch (Exception $e) {
    error_log('getBreakingNews failed: ' . $e->getMessage());
    $breakingNews = [];
}

$popularArticles = DB::getPopularArticles(5);

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<?php if (!empty($breakingNews)): ?>
<div class="breaking-ticker no-print" role="marquee" aria-label="Breaking news">
    <span class="breaking-ticker-label">Breaking</span>
    <div class="breaking-ticker-track">
        <div class="breaking-ticker-items">
            <?php foreach (array_merge($breakingNews, $breakingNews) as $b): // duplicated for seamless loop ?>
                <a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['title']); ?></a>
                <span class="breaking-sep">&#9670;</span>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<main class="main-content">
    <div class="container">

        <!-- 4-Column Victorian Newspaper Grid -->
        <div class="newspaper-grid">

            <!-- COLS 1-2: Main Story -->
            <div class="col-main">
                <div class="col-section-head">Principal Intelligence</div>
                <?php if ($page == 1 && count($articles) > 0):
                    $featured = $articles[0]; ?>
                <section class="hero">
                    <?php if (!empty($featured['image'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $featured['image']; ?>"
                             alt="<?php echo htmlspecialchars($featured['title']); ?>">
                        <p class="hero-caption">Illustrated Correspondence &mdash; Our Special Artist on the Scene</p>
                    <?php endif; ?>
                    <div class="hero-text">
                        <span class="category-badge"><?php echo $featured['category_name']; ?></span>
                        <h2><a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $featured['id']; ?>"><?php echo htmlspecialchars($featured['title']); ?></a></h2>
                        <p><?php echo htmlspecialchars($featured['excerpt'] ?? ''); ?></p>
                        <div class="meta">
                            <span>By <?php echo htmlspecialchars($featured['author']); ?></span>
                            <span><?php echo Helper::formatDate($featured['created_at']); ?></span>
                        </div>
                    </div>
                </section>
                <?php endif; ?>
            </div>

            <!-- COL 3: Latest Dispatches -->
            <?php $latestArticles = array_slice($articles, 1, 3); ?>
            <?php if (!empty($latestArticles)): ?>
            <div class="col-sidebar">
                <div class="col-section-head">Latest Dispatches</div>
                <?php foreach ($latestArticles as $article): ?>
                    <article class="article-card dir-<?php echo getTextDirection($article['title']); ?>">
                        <?php if (!empty($article['image'])): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>"
                                 alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <?php endif; ?>
                        <div class="article-content">
                            <span class="category-badge"><?php echo $article['category_name']; ?></span>
                            <h3><a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                            <p><?php echo htmlspecialchars($article['excerpt'] ?? ''); ?></p>
                            <div class="meta">
                                <span><?php echo htmlspecialchars($article['author']); ?></span>
                                <span><?php echo Helper::timeAgo($article['created_at']); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- COL 4: Special Features -->
            <?php $specialFeatures = array_slice($articles, 4, 3); ?>
            <?php if (!empty($specialFeatures)): ?>
            <div class="col-feature">
                <div class="col-section-head">Special Features</div>
                <?php foreach ($specialFeatures as $article): ?>
                    <article class="article-card dir-<?php echo getTextDirection($article['title']); ?>">
                        <div class="article-content">
                            <span class="category-badge"><?php echo $article['category_name']; ?></span>
                            <h3><a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                            <p><?php echo htmlspecialchars($article['excerpt'] ?? ''); ?></p>
                            <div class="meta">
                                <span><?php echo htmlspecialchars($article['author']); ?></span>
                                <span><?php echo Helper::timeAgo($article['created_at']); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>

            <!-- FULL-WIDTH: Further Intelligence -->
            <?php if (count($articles) > 7): ?>
            <div class="col-full-row">
                <div class="col-section-head">Further Intelligence from Our Correspondents</div>
                <div class="grid">
                    <?php for ($i = 7; $i < min(count($articles), 11); $i++):
                        $article = $articles[$i]; ?>
                        <article class="article-card">
                            <?php if (!empty($article['image'])): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>"
                                     alt="<?php echo htmlspecialchars($article['title']); ?>">
                            <?php endif; ?>
                            <div class="article-content">
                                <span class="category-badge"><?php echo $article['category_name']; ?></span>
                                <h3><a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                                <p><?php echo htmlspecialchars($article['excerpt'] ?? ''); ?></p>
                                <div class="meta">
                                    <span><?php echo htmlspecialchars($article['author']); ?></span>
                                    <span><?php echo Helper::timeAgo($article['created_at']); ?></span>
                                </div>
                            </div>
                        </article>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>

        </div><!-- /.newspaper-grid -->

        <!-- Victorian Advert Row -->
        <div class="advert-grid">
            <div class="advert-box">
                <p class="advert-head">Wanted: Skilled Correspondents</p>
                <p class="advert-body">Gentlemen of letters sought for foreign postings. Apply in writing to the Editor.</p>
                <span class="advert-price">£2 per column</span>
            </div>
            <div class="advert-box">
                <p class="advert-head">Scientific Wonders Exhibition</p>
                <p class="advert-body">The marvels of the modern age on display at the Crystal Palace, open daily.</p>
                <span class="advert-price">Admission: 6d.</span>
            </div>
            <div class="advert-box">
                <p class="advert-head">Subscribe to Our Gazette</p>
                <p class="advert-body">Receive the latest intelligence by post each morning without fail or delay.</p>
                <span class="advert-price">Annual: 12 shillings</span>
            </div>
            <div class="advert-box">
                <p class="advert-head">Telegraphic Correspondence</p>
                <p class="advert-body">Dispatches received from every corner of the Empire within the hour.</p>
                <span class="advert-price">Est. since 1842</span>
            </div>
        </div>

        <!-- Widely Read -->
        <?php if (!empty($popularArticles)): ?>
        <div class="col-full-row">
            <div class="col-section-head">Most Widely Read</div>
            <ol class="popular-list">
                <?php foreach ($popularArticles as $i => $p): ?>
                    <li>
                        <span class="popular-rank"><?php echo $i + 1; ?></span>
                        <a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['title']); ?></a>
                        <span class="popular-meta"><?php echo htmlspecialchars($p['category_name'] ?? ''); ?> &middot; <?php echo (int)$p['views']; ?> reads</span>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
        <?php endif; ?>

        <!-- Pagination -->
        <?php echo Helper::pagination($totalArticles, POSTS_PER_PAGE, $page, SITE_URL); ?>

    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>
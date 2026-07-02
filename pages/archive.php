<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';

$months = DB::getArchiveMonths();

$year  = isset($_GET['year']) ? (int) $_GET['year'] : null;
$month = isset($_GET['month']) ? (int) $_GET['month'] : null;
$page  = isset($_GET['page']) ? (int) $_GET['page'] : 1;

$articles = [];
$totalArticles = 0;
$monthNames = ['', 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

if ($year && $month) {
    $articles = DB::getArticlesByMonth($year, $month, $page);
    $totalArticles = DB::countArticlesByMonth($year, $month);
    $pageTitle = $monthNames[$month] . ' ' . $year . ' Archive';
} else {
    $pageTitle = 'Archive';
}

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <div class="col-section-head">The Gazette Archive</div>

        <div class="archive-layout">
            <!-- Month index -->
            <aside class="archive-months">
                <h4>Browse by Month</h4>
                <ul>
                    <?php foreach ($months as $m):
                        $isActive = ($year == $m['yr'] && $month == $m['mo']);
                    ?>
                    <li>
                        <a class="<?php echo $isActive ? 'active' : ''; ?>"
                           href="?year=<?php echo $m['yr']; ?>&month=<?php echo $m['mo']; ?>">
                            <?php echo $monthNames[$m['mo']] . ' ' . $m['yr']; ?>
                            <span class="archive-count"><?php echo $m['total']; ?></span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                    <?php if (empty($months)): ?>
                        <li><span class="no-comments">No published dispatches yet.</span></li>
                    <?php endif; ?>
                </ul>
            </aside>

            <!-- Selected month's articles -->
            <div class="archive-results">
                <?php if ($year && $month): ?>
                    <h3><?php echo $monthNames[$month] . ' ' . $year; ?> &mdash; <?php echo $totalArticles; ?> dispatches</h3>
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
                                            <span><?php echo htmlspecialchars($article['author']); ?></span>
                                            <span><?php echo Helper::formatDate($article['created_at'], 'M j, Y'); ?></span>
                                        </div>
                                    </div>
                                </article>
                            <?php endforeach; ?>
                        </div>
                        <?php echo Helper::pagination($totalArticles, POSTS_PER_PAGE, $page, SITE_URL . '/pages/archive.php?year=' . $year . '&month=' . $month); ?>
                    <?php else: ?>
                        <p class="no-comments">No dispatches found for this month.</p>
                    <?php endif; ?>
                <?php else: ?>
                    <p class="no-comments">Choose a month from the index to browse past dispatches.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
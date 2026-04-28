<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/functions/db.php';
require_once __DIR__ . '/functions/helpers.php';

$pageTitle = 'Home';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$articles = DB::getArticles($page);
$totalArticles = DB::countArticles();

include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>

<main class="main-content">
    <div class="container">
        <!-- Hero Section -->
        <?php if ($page == 1 && count($articles) > 0): 
            $featured = $articles[0];
        ?>
        <section class="hero">
            <div class="hero-content">
                <?php if ($featured['image']): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $featured['image']; ?>" alt="<?php echo htmlspecialchars($featured['title']); ?>">
                <?php endif; ?>
                
                <div class="hero-text">
                    <span class="category-badge"><?php echo $featured['category_name']; ?></span>
                    <h2><a href="<?php echo SITE_URL; ?>/article/<?php echo $featured['id']; ?>"><?php echo htmlspecialchars($featured['title']); ?></a></h2>
                    <p><?php echo Helper::excerpt($featured['content']); ?></p>
                    <div class="meta">
                        <span>By <?php echo htmlspecialchars($featured['author']); ?></span>
                        <span><?php echo Helper::formatDate($featured['created_at']); ?></span>
                    </div>
                </div>
            </div>
        </section>
        <?php endif; ?>
        
        <!-- Latest Articles -->
        <section class="articles-grid">
            <h2 class="section-title">Latest News</h2>
            
            <div class="grid">
                <?php 
                $startIndex = ($page == 1) ? 1 : 0; // Skip first article on page 1 (featured)
                for ($i = $startIndex; $i < count($articles); $i++): 
                    $article = $articles[$i];
                ?>
                    <article class="article-card">
                        <?php if ($article['image']): ?>
                            <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                        <?php endif; ?>
                        
                        <div class="article-content">
                            <span class="category-badge"><?php echo $article['category_name']; ?></span>
                            <h3><a href="<?php echo SITE_URL; ?>/article/<?php echo $article['id']; ?>"><?php echo htmlspecialchars($article['title']); ?></a></h3>
                            <p><?php echo Helper::excerpt($article['content'], 120); ?></p>
                            <div class="meta">
                                <span><?php echo htmlspecialchars($article['author']); ?></span>
                                <span><?php echo Helper::timeAgo($article['created_at']); ?></span>
                            </div>
                        </div>
                    </article>
                <?php endfor; ?>
            </div>
        </section>
        
        <!-- Pagination -->
        <?php echo Helper::pagination($totalArticles, POSTS_PER_PAGE, $page, SITE_URL); ?>
    </div>
</main>

<?php include __DIR__ . '/includes/footer.php'; ?>

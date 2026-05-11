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


// Handle comment submit
$commentError = '';
$commentSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_body'])) {
    $body = trim($_POST['comment_body']);
    $userId = $_SESSION['user_id'] ?? null;
    $guestName = $userId ? null : Validate::sanitize($_POST['guest_name'] ?? '');
    $guestEmail = $userId ? null : Validate::sanitize($_POST['guest_email'] ?? '');

    if (empty($body)) {
        $commentError = 'Comment cannot be empty.';
    } elseif (!$userId && empty($guestName)) {
        $commentError = 'Please enter your name.';
    } else {
        DB::addComment([
            'article_id'  => $id,
            'user_id'     => $userId,
            'guest_name'  => $guestName,
            'guest_email' => $guestEmail,
            'body'        => $body
        ]);
        $commentSuccess = 'Comment submitted! Awaiting approval.';
    }
}

$comments = DB::getComments($id);

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

            <?php if (!empty($article['image'])): ?>
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
        <!-- COMMENTS SECTION -->
        <section class="comments-section">
            <h2>Comments (<?php echo count($comments); ?>)</h2>

            <?php if ($comments): ?>
                <div class="comments-list">
                    <?php foreach ($comments as $c): ?>
                        <div class="comment">
                            <div class="comment-meta">
                                <strong><?php echo htmlspecialchars($c['username'] ?? $c['guest_name'] ?? 'Anonymous'); ?></strong>
                                <span><?php echo Helper::formatDate($c['created_at'], 'M j, Y'); ?></span>
                            </div>
                            <p><?php echo nl2br(htmlspecialchars($c['body'])); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="no-comments">No comments yet. Be the first!</p>
            <?php endif; ?>

            <!-- COMMENT FORM -->
            <?php if ($commentError): ?>
                <div class="alert alert-error"><?php echo $commentError; ?></div>
            <?php endif; ?>
            <?php if ($commentSuccess): ?>
                <div class="alert alert-success"><?php echo $commentSuccess; ?></div>
            <?php endif; ?>

            <form method="POST" class="comment-form">
                <?php if (empty($_SESSION['user_id'])): ?>
                    <div class="form-group">
                        <label>Name *</label>
                        <input type="text" name="guest_name" required>
                    </div>
                    <div class="form-group">
                        <label>Email (optional)</label>
                        <input type="email" name="guest_email">
                    </div>
                <?php endif; ?>
                <div class="form-group">
                    <label>Comment *</label>
                    <textarea name="comment_body" rows="5" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </form>
        </section>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
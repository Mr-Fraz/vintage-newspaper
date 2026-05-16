<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/validation.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Multi-language: ?lang=ur  (default en)
$reqLang  = Validate::sanitize($_GET['lang'] ?? 'en');
$reqLang  = preg_replace('/[^a-z\-]/', '', strtolower($reqLang)); // safety

if ($reqLang && $reqLang !== 'en') {
    // Try to find translation; fall back to original
    $article = DB::getArticle($id);
    if ($article) {
        $trans = DB::getTranslation($id, $reqLang);
        if ($trans && $trans['status'] === 'published') {
            // Overlay translated fields on top of original
            $article['title']            = $trans['title'];
            $article['content']          = $trans['content'];
            $article['excerpt']          = $trans['excerpt'];
            $article['seo_title']        = $trans['seo_title'] ?? $trans['title'];
            $article['meta_description'] = $trans['meta_description'] ?? $trans['excerpt'];
            $article['_lang']            = $reqLang;
        } else {
            $article['_lang'] = $article['lang'] ?? 'en';
        }
    }
} else {
    $article = DB::getArticle($id);
    if ($article) $article['_lang'] = $article['lang'] ?? 'en';
}

if (!$article) {
    header('Location: ' . SITE_URL);
    exit;
}

$articleLang = $article['_lang'] ?? 'en';
$rtlLangs    = ['ar', 'ur', 'fa', 'he'];

// Auto-detect Urdu/Arabic if lang='en' but content has RTL chars
if ($articleLang === 'en' && !empty($article['content'])) {
    $sample = strip_tags($article['content']);
    // Urdu/Arabic Unicode range check
    if (preg_match('/[\x{0600}-\x{06FF}\x{0750}-\x{077F}\x{FB50}-\x{FDFF}\x{FE70}-\x{FEFF}]/u', $sample)) {
        $articleLang = 'ur';
    }
}

$isRtl = in_array($articleLang, $rtlLangs);

// Font config per language
$langFonts = [
    'ur' => [
        'google' => 'https://fonts.googleapis.com/css2?family=Gulzar&display=swap',
        'family' => "'Gulzar', serif",
        'size'   => '1.35rem',
        'lineH'  => '3.0',
    ],
    'ar' => [
        'google' => 'https://fonts.googleapis.com/css2?family=Noto+Naskh+Arabic:wght@400;700&display=swap',
        'family' => "'Noto Naskh Arabic', serif",
        'size'   => '1.1rem',
        'lineH'  => '2.0',
    ],
];
$fontCfg = $langFonts[$articleLang] ?? null;

// Fetch all available translations for lang switcher
$availableTranslations = DB::getTranslations($id);

$pageTitle = $article['title'];


// Handle comment submit
$commentError = '';
$commentSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['comment_body'])) {
    $body = trim($_POST['comment_body']);
    $userId = $_SESSION['user_id'] ?? null;
    $guestName = $userId ? null : Validate::sanitize($_POST['guest_name'] ?? '');
    $guestEmail = null; // email field removed

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

// Inject Urdu/RTL font and styles
if ($fontCfg): ?>
<style>
/*
 * Urdu font loading strategy:
 * 1. Local file (if uploaded to assets/fonts/)
 * 2. jsDelivr CDN (works on most shared hosts)
 * 3. System fonts (Windows/Android/iOS have Urdu fonts built-in)
 */
@font-face {
    font-family: 'Gulzar';
    src: url('<?php echo SITE_URL; ?>/assets/fonts/Gulzar-Regular.woff2') format('woff2'),
         url('<?php echo SITE_URL; ?>/assets/fonts/Gulzar-Regular.ttf') format('truetype'),
         url('https://cdn.jsdelivr.net/gh/google/fonts@main/ofl/gulzar/Gulzar-Regular.ttf') format('truetype');
    font-weight: normal;
    font-style: normal;
    font-display: swap;
}

/* System Urdu fonts — these are ALREADY installed on most devices:
   Windows 8+  : Urdu Typesetting (Nastaliq)
   Windows 10+ : Alvi Lahori Nastaleeq
   Android     : Noto Nastaliq Urdu
   iOS/macOS   : Geeza Pro (Naskh, not Nastaliq but renders correctly)
*/
:root {
    --urdu-font: 'Gulzar',
                 'Urdu Typesetting',
                 'Alvi Lahori Nastaleeq',
                 'Jameel Noori Nastaleeq',
                 'Noto Nastaliq Urdu',
                 'Geeza Pro',
                 'Arabic Typesetting',
                 serif;
}

.article-single[lang="ur"],
.article-single[lang="ar"] {
    direction: rtl;
}
.article-single[lang="ur"] h1 {
    font-family: var(--urdu-font);
    font-size: clamp(1.8rem, 4vw, 2.8rem);
    line-height: 2.4;
    text-align: right;
    letter-spacing: 0;
}
.article-single[lang="ur"] .article-header,
.article-single[lang="ur"] .article-meta,
.article-single[lang="ar"] .article-header,
.article-single[lang="ar"] .article-meta {
    text-align: right;
    direction: rtl;
}
.article-content-lang {
    font-family: var(--urdu-font);
    font-size: 1.3rem;
    line-height: 3.0;
    text-align: right;
    direction: rtl;
}
.article-content-lang p,
.article-content-lang li,
.article-content-lang span,
.article-content-lang div {
    font-family: var(--urdu-font);
    font-size: 1.3rem;
    line-height: 3.0;
}
.article-content-lang h2,
.article-content-lang h3,
.article-content-lang h4 {
    font-family: var(--urdu-font);
    line-height: 2.4;
    margin-top: 1.5em;
    text-align: right;
}
.lang-switcher {
    direction: ltr;
    text-align: left;
    margin: .6rem 0;
    font-family: sans-serif;
}
</style>
<?php endif; ?>

<main class="main-content">
    <div class="container">
        <article class="article-single" lang="<?php echo htmlspecialchars($articleLang); ?>"
                 dir="<?php echo $isRtl ? 'rtl' : 'ltr'; ?>">
            <header class="article-header">
                <span class="category-badge"><?php echo $article['category_name']; ?></span>
                <h1><?php echo htmlspecialchars($article['title']); ?></h1>
                <?php
                // Show switcher only if article has multiple language versions
                $langNames = ['en'=>'English','ur'=>'اردو','ar'=>'العربية','fr'=>'Français','de'=>'Deutsch','zh'=>'中文','es'=>'Español'];
                $publishedTrans = array_filter($availableTranslations, fn($t) => $t['status'] === 'published');
                $originalLang = $article['lang'] ?? 'en';
                // Only show if there's at least one translation in a DIFFERENT language
                $otherLangs = array_filter($publishedTrans, fn($t) => $t['lang'] !== $originalLang);
                if (!empty($otherLangs)):
                ?>
                <div class="lang-switcher" style="margin:0.5rem 0;font-size:0.9rem">
                    <strong>🌐</strong>
                    <a href="?id=<?php echo $id; ?>&lang=<?php echo $originalLang; ?>"
                       <?php echo $articleLang===$originalLang?'style="font-weight:bold"':''; ?>>
                        <?php echo $langNames[$originalLang] ?? strtoupper($originalLang); ?>
                    </a>
                    <?php foreach ($otherLangs as $t):
                        $lname = $langNames[$t['lang']] ?? strtoupper($t['lang']);
                    ?>
                        · <a href="?id=<?php echo $id; ?>&lang=<?php echo $t['lang']; ?>"
                             <?php echo $articleLang===$t['lang']?'style="font-weight:bold"':''; ?>>
                            <?php echo $lname; ?>
                          </a>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>

                <div class="article-meta">
                    <span class="author">By <?php echo htmlspecialchars($article['author']); ?></span>
                    <span class="date"><?php echo Helper::formatDate($article['created_at'], 'F j, Y g:i A'); ?></span>
                </div>
            </header>

            <?php if (!empty($article['image'])): ?>
                <div class="article-image">
                    <?php
                    $alt = htmlspecialchars($article['image_alt'] ?? $article['title']);
                    $imgSrc = SITE_URL . $article['image'];
                    ?>
                    <img src="<?php echo $imgSrc; ?>"
                        alt="<?php echo $alt; ?>"
                        loading="lazy">
                </div>
            <?php endif; ?>

            <div class="article-content article-content-lang" dir="<?php echo $isRtl ? 'rtl' : 'ltr'; ?>"
                 style="<?php echo $isRtl && !$fontCfg ? 'text-align:right' : ''; ?>">
                <?php echo $article['content']; ?>
            </div>

            <footer class="article-footer">
                <a href="<?php echo SITE_URL; ?>" class="btn">← Back to Home</a>
                <a href="<?php echo SITE_URL; ?>/pages/category.php?slug=<?php echo $article['category_name']; ?>" class="btn">More in <?php echo $article['category_name']; ?></a>
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
                <?php endif; ?>
                <div class="form-group">
                    <label>Comment *</label>
                    <textarea name="comment_body" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm-comment">Comment</button>
            </form>
        </section>
    </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
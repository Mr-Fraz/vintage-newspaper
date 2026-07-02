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

// +1 view (best-effort, non-blocking)
DB::incrementViews($id);

// Related articles from same category
$relatedArticles = DB::getRelatedArticles($id, $article['category_id'] ?? 0, 3);

// SEO / Open Graph — pulls existing seo_title / meta_description / og_image columns
$pageTitle       = !empty($article['seo_title']) ? $article['seo_title'] : $article['title'];
$metaDescription = !empty($article['meta_description']) ? $article['meta_description'] : ($article['excerpt'] ?? '');
$ogImage         = !empty($article['og_image']) ? SITE_URL . $article['og_image'] : (!empty($article['image']) ? SITE_URL . $article['image'] : '');
$canonicalUrl    = SITE_URL . '/pages/article.php?id=' . $id;

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
                    <span class="author">By
                        <?php if (!empty($article['author_id'])): ?>
                            <a href="<?php echo SITE_URL; ?>/pages/author.php?id=<?php echo (int)$article['author_id']; ?>"><?php echo htmlspecialchars($article['author']); ?></a>
                        <?php else: ?>
                            <?php echo htmlspecialchars($article['author']); ?>
                        <?php endif; ?>
                    </span>
                    <span class="date"><?php echo Helper::formatDate($article['created_at'], 'F j, Y g:i A'); ?></span>
                    <span class="views">👁 <?php echo (int)($article['views'] ?? 0) + 1; ?> reads</span>
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

            <div class="article-content article-content-lang<?php echo !$isRtl ? ' drop-cap' : ''; ?>" dir="<?php echo $isRtl ? 'rtl' : 'ltr'; ?>"
                 style="<?php echo $isRtl && !$fontCfg ? 'text-align:right' : ''; ?>">
                <?php echo $article['content']; ?>
            </div>

            <!-- Print + Share toolbar (icon-only) -->
            <div class="article-toolbar no-print">
                <button type="button" class="btn-tool" onclick="window.print()" aria-label="Print this article" title="Print">
                    <i data-lucide="printer"></i>
                </button>
                <a class="btn-tool" target="_blank" rel="noopener"
                   href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode($canonicalUrl); ?>"
                   aria-label="Share on Facebook" title="Share on Facebook">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06c0 5.02 3.66 9.18 8.44 9.94v-7.03H7.9v-2.91h2.54V9.85c0-2.51 1.49-3.9 3.77-3.9 1.09 0 2.24.2 2.24.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.87h2.78l-.44 2.91h-2.34V22c4.78-.76 8.44-4.92 8.44-9.94z"/>
                    </svg>
                </a>
                <a class="btn-tool" target="_blank" rel="noopener"
                   href="https://twitter.com/intent/tweet?url=<?php echo urlencode($canonicalUrl); ?>&text=<?php echo urlencode($article['title']); ?>"
                   aria-label="Share on X" title="Share on X">
                    <svg viewBox="0 0 24 24" width="15" height="15" fill="currentColor" aria-hidden="true">
                        <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                    </svg>
                </a>
                <a class="btn-tool btn-tool-whatsapp" target="_blank" rel="noopener"
                   href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' ' . $canonicalUrl); ?>"
                   aria-label="Share on WhatsApp" title="Share on WhatsApp">
                    <svg viewBox="0 0 24 24" width="16" height="16" fill="currentColor" aria-hidden="true">
                        <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.9-4.45 9.9-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2zm5.8 14.05c-.24.68-1.4 1.33-1.93 1.4-.5.08-1.12.11-1.8-.11-.42-.13-.95-.31-1.64-.6-2.88-1.24-4.76-4.14-4.9-4.33-.14-.19-1.17-1.55-1.17-2.96 0-1.4.73-2.09 1-2.38.26-.28.57-.35.76-.35.19 0 .38 0 .55.01.18.01.42-.07.65.5.24.58.81 2.01.88 2.16.07.15.12.32.02.51-.09.19-.14.31-.28.48-.14.16-.29.36-.42.49-.14.13-.28.28-.12.55.16.28.71 1.17 1.52 1.9 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.6-.07.16-.19.68-.79.87-1.06.18-.28.36-.23.6-.14.24.09 1.55.73 1.82.87.27.13.44.19.51.3.07.11.07.62-.17 1.3z"/>
                    </svg>
                </a>
                <button type="button" class="btn-tool" id="copy-link-btn" data-url="<?php echo htmlspecialchars($canonicalUrl); ?>" aria-label="Copy link" title="Copy link">
                    <i data-lucide="copy"></i>
                </button>
            </div>

            <footer class="article-footer no-print">
                <a href="<?php echo SITE_URL; ?>" class="btn">← Home</a>
                <a href="<?php echo SITE_URL; ?>/pages/category.php?slug=<?php echo $article['category_name']; ?>" class="btn">More <?php echo $article['category_name']; ?></a>
            </footer>
        </article>

        <!-- RELATED ARTICLES -->
        <?php if (!empty($relatedArticles)): ?>
        <section class="related-articles no-print">
            <div class="col-section-head">Related Dispatches</div>
            <div class="related-grid">
                <?php foreach ($relatedArticles as $rel): ?>
                    <a class="related-card" href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $rel['id']; ?>">
                        <?php if (!empty($rel['image'])): ?>
                            <img src="<?php echo SITE_URL . $rel['image']; ?>" alt="<?php echo htmlspecialchars($rel['image_alt'] ?? $rel['title']); ?>" loading="lazy">
                        <?php endif; ?>
                        <div class="related-card-body">
                            <span class="category-badge"><?php echo htmlspecialchars($rel['category_name'] ?? ''); ?></span>
                            <h4><?php echo htmlspecialchars($rel['title']); ?></h4>
                            <span class="meta"><?php echo Helper::formatDate($rel['created_at']); ?></span>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
        <!-- COMMENTS SECTION -->
        <section class="comments-section no-print">
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    var copyBtn = document.getElementById('copy-link-btn');
    if (!copyBtn) return;
    copyBtn.addEventListener('click', function () {
        var url = copyBtn.getAttribute('data-url');
        navigator.clipboard.writeText(url).then(function () {
            copyBtn.innerHTML = '<i data-lucide="check"></i>';
            if (typeof lucide !== 'undefined') lucide.createIcons();
            setTimeout(function () {
                copyBtn.innerHTML = '<i data-lucide="copy"></i>';
                if (typeof lucide !== 'undefined') lucide.createIcons();
            }, 1500);
        });
    });
});
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
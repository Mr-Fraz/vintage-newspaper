<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/db.php';
require_once __DIR__ . '/../../functions/validation.php';
require_once __DIR__ . '/../../functions/helpers.php';

$pageTitle = 'Add / Edit Translation';
$error     = '';
$success   = '';

$article_id = (int)($_GET['article_id'] ?? 0);
if (!$article_id) { header('Location: list.php'); exit; }

$article = DB::getArticleForEdit($article_id);
if (!$article) { header('Location: list.php'); exit; }

$lang        = Validate::sanitize($_GET['lang'] ?? 'ur');
$translation = DB::getTranslation($article_id, $lang);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token.';
    } else {
        $lang              = Validate::sanitize($_POST['lang']);
        $title             = Validate::sanitize($_POST['title']);
        $slug              = Validate::slug($title) . '-' . $lang;
        $content           = $_POST['content'];
        $excerpt           = Validate::sanitize($_POST['excerpt'] ?? '');
        $seo_title         = Validate::sanitize($_POST['seo_title'] ?? '');
        $meta_description  = Validate::sanitize($_POST['meta_description'] ?? '');
        $status            = Validate::sanitize($_POST['status']);

        $ok = DB::upsertTranslation($article_id, $lang, [
            'title'            => $title,
            'slug'             => $slug,
            'content'          => $content,
            'excerpt'          => $excerpt,
            'seo_title'        => $seo_title ?: null,
            'meta_description' => $meta_description ?: null,
            'status'           => $status,
        ]);

        if ($ok) {
            $success     = 'Translation saved!';
            $translation = DB::getTranslation($article_id, $lang);
        } else {
            $error = 'Save failed. Check DB.';
        }
    }
}

$allTranslations = DB::getTranslations($article_id);

$supportedLangs = [
    'ur' => 'Urdu (اردو)',
    'ar' => 'Arabic (العربية)',
    'fr' => 'French',
    'de' => 'German',
    'zh' => 'Chinese',
    'es' => 'Spanish',
    'hi' => 'Hindi',
];

$rtlLangs  = ['ar', 'ur', 'fa', 'he'];
$isRtl     = in_array($lang, $rtlLangs);
$direction = $isRtl ? 'rtl' : 'ltr';

// Per-language font config (Google Fonts)
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
$fontCfg     = $langFonts[$lang] ?? null;
$fontFamily  = $fontCfg ? $fontCfg['family']  : 'inherit';
$fontSize    = $fontCfg ? $fontCfg['size']    : '1rem';
$lineHeight  = $fontCfg ? $fontCfg['lineH']   : '1.6';
$googleFont  = $fontCfg ? $fontCfg['google']  : '';

include __DIR__ . '/../includes/admin-header.php';
?>
<?php if ($fontCfg): ?>
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="<?php echo htmlspecialchars($googleFont); ?>">
<?php endif; ?>

<style>
.translate-toolbar {
    display: flex;
    align-items: center;
    gap: .75rem;
    flex-wrap: wrap;
    margin-bottom: 1.25rem;
    padding: .75rem 1rem;
    background: #f8f4ec;
    border: 1px solid #ddd;
    border-radius: 6px;
}
#btn-auto-translate {
    background: #2271b1;
    color: #fff;
    border: none;
    padding: .45rem 1rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: .9rem;
}
#btn-auto-translate:disabled { opacity: .6; cursor: not-allowed; }
#translate-status { font-size: .85rem; color: #555; }
.lang-pills a {
    display: inline-block;
    padding: .2rem .6rem;
    margin: 2px;
    border: 1px solid #aaa;
    border-radius: 3px;
    font-size: .85rem;
    text-decoration: none;
    color: #333;
}
.lang-pills a.active { background: #2271b1; color: #fff; border-color: #2271b1; }
</style>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Translate: <?php echo htmlspecialchars($article['title']); ?></h1>
            <a href="edit.php?id=<?php echo $article_id; ?>" class="btn">← Back to Edit</a>
        </header>

        <!-- Translation pills -->
        <?php if ($allTranslations): ?>
        <div class="lang-pills" style="margin-bottom:1rem">
            <strong>Saved translations:</strong>
            <?php foreach ($allTranslations as $t): ?>
                <a href="translate.php?article_id=<?php echo $article_id; ?>&lang=<?php echo $t['lang']; ?>"
                   class="<?php echo $t['lang']===$lang?'active':''; ?>">
                    <?php echo strtoupper($t['lang']); ?> &mdash; <?php echo ucfirst($t['status']); ?>
                </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Toolbar: lang switch + AI button -->
        <div class="translate-toolbar">
            <label style="margin:0;font-weight:600">Language:</label>
            <select onchange="location.href='translate.php?article_id=<?php echo $article_id; ?>&lang='+this.value">
                <?php foreach ($supportedLangs as $code => $name): ?>
                    <option value="<?php echo $code; ?>" <?php echo $code===$lang?'selected':''; ?>>
                        <?php echo $name; ?>
                    </option>
                <?php endforeach; ?>
            </select>

            <button id="btn-auto-translate" type="button">✨ Auto-Translate with AI</button>
            <span id="translate-status"></span>
        </div>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="POST" id="trans-form" onsubmit="tinymce.triggerSave()">
            <?php echo csrfField(); ?>
            <input type="hidden" name="lang" value="<?php echo htmlspecialchars($lang); ?>">

            <div class="form-group">
                <label>Title (<?php echo $supportedLangs[$lang] ?? $lang; ?>) *</label>
                <input type="text" name="title" id="f-title" required
                    value="<?php echo htmlspecialchars($translation['title'] ?? ''); ?>"
                    style="<?php echo $isRtl ? "direction:rtl;text-align:right;font-family:{$fontFamily};font-size:{$fontSize}" : ''; ?>">
            </div>

            <div class="form-group">
                <label>Excerpt</label>
                <textarea name="excerpt" id="f-excerpt" rows="3"
                    style="<?php echo $isRtl ? "direction:rtl;text-align:right;font-family:{$fontFamily};font-size:{$fontSize};line-height:{$lineHeight}" : ''; ?>"
                ><?php echo htmlspecialchars($translation['excerpt'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Content *</label>
                <textarea id="trans_content" name="content" rows="15"><?php echo htmlspecialchars($translation['content'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>SEO Title</label>
                <input type="text" name="seo_title"
                    value="<?php echo htmlspecialchars($translation['seo_title'] ?? ''); ?>">
            </div>

            <div class="form-group">
                <label>Meta Description</label>
                <textarea name="meta_description" rows="2"><?php echo htmlspecialchars($translation['meta_description'] ?? ''); ?></textarea>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <?php foreach (['draft','pending','published','archived'] as $st): ?>
                        <option value="<?php echo $st; ?>"
                            <?php echo ($translation['status'] ?? 'draft')===$st?'selected':''; ?>>
                            <?php echo ucfirst($st); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">💾 Save Translation</button>
            </div>
        </form>
    </main>
</div>

<script>
const IS_RTL       = <?php echo $isRtl ? 'true' : 'false'; ?>;
const LANG_DIR     = IS_RTL ? 'rtl' : 'ltr';
const GOOGLE_FONT  = <?php echo json_encode($googleFont); ?>;
const EDITOR_FONT  = <?php echo json_encode($fontFamily); ?>;
const EDITOR_SIZE  = <?php echo json_encode($fontSize); ?>;
const EDITOR_LINE  = <?php echo json_encode($lineHeight); ?>;

// Build content_style — import Google Font inside TinyMCE iframe
const contentStyle = `
    ${GOOGLE_FONT ? "@import url('" + GOOGLE_FONT + "');" : ''}
    body {
        font-family: ${EDITOR_FONT};
        font-size: ${EDITOR_SIZE};
        line-height: ${EDITOR_LINE};
        direction: ${LANG_DIR};
        text-align: ${IS_RTL ? 'right' : 'left'};
        padding: 1rem;
    }
`;

tinymce.init({
    selector: '#trans_content',
    convert_urls: false,
    relative_urls: false,
    remove_script_host: false,
    directionality: LANG_DIR,
    plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount directionality',
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | checklist numlist bullist indent outdent | ltr rtl | emoticons charmap | removeformat',
    height: 550,
    menubar: true,
    skin: 'oxide',
    content_css: 'default',
    content_style: contentStyle,
    branding: false,
    promotion: false,
    images_upload_url: '<?php echo SITE_URL; ?>/api/upload.php',
    images_upload_credentials: true,
    images_upload_handler: function(blobInfo, progress) {
        return new Promise(function(resolve, reject) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', '<?php echo SITE_URL; ?>/api/upload.php');
            xhr.withCredentials = true;
            xhr.upload.onprogress = function(e) { progress(e.loaded / e.total * 100); };
            xhr.onload = function() {
                var json;
                try { json = JSON.parse(xhr.responseText); } catch(e) { reject('Invalid JSON'); return; }
                if (!json || !json.file) { reject('Upload failed: ' + xhr.responseText); return; }
                resolve(json.file);
            };
            xhr.onerror = function() { reject('Upload failed'); };
            var formData = new FormData();
            formData.append('file', blobInfo.blob(), blobInfo.filename());
            formData.append('csrf_token', document.querySelector('input[name=csrf_token]').value);
            xhr.send(formData);
        });
    },
    setup: function(editor) {
        editor.on('init', function() {
            editor.getBody().setAttribute('dir', LANG_DIR);
        });
    }
});

// ── Auto-translate handler ───────────────────────────────────────────────────
document.getElementById('btn-auto-translate').addEventListener('click', async function() {
    const btn    = this;
    const status = document.getElementById('translate-status');

    btn.disabled    = true;
    status.textContent = '⏳ AI translating…';

    const formData = new FormData();
    formData.append('csrf_token',  document.querySelector('input[name=csrf_token]').value);
    formData.append('article_id', '<?php echo $article_id; ?>');
    formData.append('lang',       '<?php echo $lang; ?>');

    try {
        const res  = await fetch('<?php echo SITE_URL; ?>/api/auto-translate.php', {
            method: 'POST',
            credentials: 'same-origin',
            body: formData
        });
        const data = await res.json();

        if (data.error) {
            status.textContent = '❌ ' + data.error;
            btn.disabled = false;
            return;
        }

        // Fill plain fields
        document.getElementById('f-title').value   = data.title   || '';
        document.getElementById('f-excerpt').value = data.excerpt || '';

        // Fill TinyMCE (convert plain text to basic paragraphs if no HTML)
        const editor = tinymce.get('trans_content');
        let html = data.content || '';
        if (html && !html.trim().startsWith('<')) {
            html = '<p>' + html.replace(/\n{2,}/g, '</p><p>').replace(/\n/g, '<br>') + '</p>';
        }
        if (editor) {
            editor.setContent(html);
        } else {
            document.getElementById('trans_content').value = html;
        }

        status.textContent = '✅ Done! Review & save.';
    } catch(e) {
        status.textContent = '❌ ' + e.message;
    }
    btn.disabled = false;
});
</script>
</body>
</html>
<?php
require_once __DIR__ . '/../includes/auth-check.php';
require_once __DIR__ . '/../../functions/db.php';
require_once __DIR__ . '/../../functions/validation.php';
require_once __DIR__ . '/../../functions/helpers.php';
date_default_timezone_set('Asia/Karachi');

$pageTitle = 'Edit Post';
$error = '';
$success = '';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
try {
    $article = DB::getArticleForEdit($id);
} catch (Exception $e) {
    header('Location: list.php');
    exit;
}

if (!$article) {
    header('Location: list.php');
    exit;
}

$categories = DB::getCategories();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF protection
    if (empty($_POST['csrf_token']) || !verifyCSRFToken($_POST['csrf_token'])) {
        $error = 'Invalid CSRF token. Please try again.';
    }

    $title = Validate::sanitize($_POST['title']);
    $slug = Validate::slug($title);
    $content = $_POST['content'];
    $excerpt = Validate::sanitize($_POST['excerpt']);
    $category_id = (int)$_POST['category_id'];
    $status = Validate::sanitize($_POST['status']);
    $tags = isset($_POST['tags']) ? trim($_POST['tags']) : '';
    $seo_title = isset($_POST['seo_title']) ? Validate::sanitize($_POST['seo_title']) : null;
    $meta_description = isset($_POST['meta_description']) ? Validate::sanitize($_POST['meta_description']) : null;
    if (empty($status)) {
        $status = 'draft';
    }
    // Handle image upload or selection
    $imageName = '';
    $mediaId   = null;
    $imageAlt  = Validate::sanitize($_POST['image_alt'] ?? '');

    // Option A: new file uploaded
    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
        $upload = Helper::uploadImage($_FILES['image'], $imageAlt, $_SESSION['user_id']);
        if ($upload['success']) {
            $imageName = $upload['filename_medium']; // use optimized version
            $mediaId   = $upload['media_id'];
        } else {
            $error = $upload['message'];
        }
    }
    // Option B: selected from media library
    elseif (!empty($_POST['media_id'])) {
        $mediaId = (int)$_POST['media_id'];
        $media   = DB::getMedia($mediaId);
        if ($media) {
            $imageName = $media['filename_medium'];
            if ($imageAlt) DB::updateMediaAlt($mediaId, $imageAlt);
        }
    }

    // Save revision of previous content
    $currentUserId = $_SESSION['user_id'] ?? null;
    DB::createRevision($id, $currentUserId);

    $data = [
        'title' => $title,
        'slug' => $slug,
        'content' => $content,
        'excerpt' => $excerpt,
        'image' => $imageName,
        'media_id' => $mediaId,
        'image_alt' => $imageAlt,
        'category_id' => $category_id,
        'status' => $status,
        'seo_title' => $seo_title,
        'meta_description' => $meta_description,
        'og_image' => null
    ];

    if (DB::updateArticle($id, $data)) {
        // Update tags: clear and re-attach
        DB::clearArticleTags($id);
        if (!empty($tags)) {
            $tagNames = array_filter(array_map('trim', explode(',', $tags)));
            DB::attachTagsToArticle($id, $tagNames);
        }

        $success = 'Article updated successfully!';
        header('Location: list.php');
        exit;
    } else {
        $error = 'Failed to update article';
    }
}

include __DIR__ . '/../includes/admin-header.php';
?>

<div class="admin-wrapper">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>

    <main class="admin-main">
        <header class="admin-page-header">
            <h1>Edit Post</h1>
            
        </header>

        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo $error; ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" class="admin-form" onsubmit="tinymce.triggerSave()">
            <?php echo csrfField(); ?>
            <div class="form-group">
                <label for="title">Title *</label>
                <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($article['title']); ?>" required>
            </div>

            <div class="form-group">
                <label for="category_id">Category *</label>
                <select id="category_id" name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id'] == $article['category_id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="excerpt">Excerpt</label>
                <textarea id="excerpt" name="excerpt" rows="3"><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="tags">Tags (comma-separated)</label>
                <?php $existingTags = DB::getArticleTags($id);
                $tagList = implode(', ', array_map(function ($t) {
                    return $t['name'];
                }, $existingTags)); ?>
                <input type="text" id="tags" name="tags" value="<?php echo htmlspecialchars($tagList, ENT_QUOTES); ?>" placeholder="e.g. politics, local, opinion">
            </div>

            <div class="form-group">
                <label for="seo_title">SEO Title</label>
                <input type="text" id="seo_title" name="seo_title" value="<?php echo htmlspecialchars($article['seo_title'] ?? '', ENT_QUOTES); ?>" placeholder="Optional SEO title">
            </div>

            <div class="form-group">
                <label for="meta_description">Meta Description</label>
                <textarea id="meta_description" name="meta_description" rows="2"><?php echo htmlspecialchars($article['meta_description'] ?? '', ENT_QUOTES); ?></textarea>
            </div>

            <div class="form-group">
                <label for="content">Content *</label>
                <textarea id="content" name="content" rows="15"><?php echo $article['content']; ?></textarea>
            </div>

            <div class="form-group">
                <label for="image_alt">Image Alt Text</label>
                <input type="text" id="image_alt" name="image_alt"
                    value="<?php echo htmlspecialchars($article['image_alt'] ?? ''); ?>"
                    placeholder="Describe the image for accessibility">
            </div>

            <div class="form-group">
                <label>Featured Image</label>

                <!-- Current image preview (edit.php only) -->
                <?php if (!empty($article['image'])): ?>
                    <div class="current-image-preview">
                        <p class="preview-label">Current:</p>
                        <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>"
                            alt="<?php echo htmlspecialchars($article['image_alt'] ?? ''); ?>">
                    </div>
                <?php endif; ?>

                <!-- Upload new -->
                <label>Upload New Image</label>
                <input type="file" id="image" name="image" accept="image/*">

                <!-- OR pick from library -->
                <label>— or select from Media Library —</label>
                <input type="hidden" name="media_id" id="selected_media_id" value="">
                <div class="media-library-grid">
                    <?php foreach (DB::getMediaLibrary() as $m): ?>
                        <div class="media-item" onclick="selectMedia(<?php echo $m['id']; ?>, this)">
                            <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $m['filename_thumb']; ?>"
                                alt="<?php echo htmlspecialchars($m['alt_text']); ?>">
                            <small><?php echo htmlspecialchars($m['alt_text'] ?: $m['filename']); ?></small>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="form-group">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php
                    $statuses = ['draft', 'pending', 'published', 'archived'];
                    foreach ($statuses as $st) {
                        $sel = ($article['status'] == $st) ? 'selected' : '';
                        echo '<option value="' . $st . '" ' . $sel . '>' . ucfirst($st) . '</option>';
                    }
                    ?>
                </select>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">Save</button>
                <a href="list.php" class="btn">Cancel</a>
            </div>
        </form>
    </main>
</div>
<style>
    /* Match TinyMCE chrome to site parchment theme */
    .tox-tinymce { border-color: #c4a882 !important; }
    .tox .tox-editor-header,
    .tox .tox-toolbar,
    .tox .tox-toolbar__primary,
    .tox .tox-menubar,
    .tox .tox-statusbar {
        background: #f0e6d0 !important;
        border-color: #c4a882 !important;
    }
    .tox .tox-tbtn svg,
    .tox .tox-mbtn,
    .tox .tox-statusbar a,
    .tox .tox-statusbar__path-item {
        color: #1c0f07 !important;
        fill: #1c0f07 !important;
    }
    .tox .tox-tbtn:hover,
    .tox .tox-mbtn:hover {
        background: #d4c89a !important;
    }
    .tox .tox-edit-area__iframe { background: #f0e6d0 !important; }
</style>
<script>
    tinymce.init({
        selector: '#content',
        convert_urls: false, // ← ADD THIS
        relative_urls: false, // ← ADD THIS
        remove_script_host: false, // ← ADD THIS
        plugins: 'anchor autolink charmap codesample emoticons image link lists media searchreplace table visualblocks wordcount',
        toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
        height: 500,
        menubar: true,
        skin: 'oxide',
        content_css: 'default',
        body_class: 'article-content',
        content_style: `
            body.article-content {
                background: #f0e6d0;
                color: #1c0f07;
                max-width: 700px;
                margin: 0 auto;
                padding: 16px 20px;
                font-size: 1.125rem;
                line-height: 1.55;
            }
            body.article-content img,
            body.article-content figure img {
                max-width: 100% !important;
                display: block;
                margin: 20px auto;
                border: 1px solid #2a1e0e;
            }
            body.article-content img.alignleft,
            body.article-content figure.alignleft,
            body.article-content figure:has(> img.alignleft) {
                float: left !important;
                display: inline !important;
                margin: 6px 20px 12px 0 !important;
                max-width: 45% !important;
            }
            body.article-content img.alignright,
            body.article-content figure.alignright,
            body.article-content figure:has(> img.alignright) {
                float: right !important;
                display: inline !important;
                margin: 6px 0 12px 20px !important;
                max-width: 45% !important;
            }
            body.article-content img.aligncenter,
            body.article-content figure.aligncenter,
            body.article-content figure:has(> img.aligncenter) {
                float: none !important;
                display: block !important;
                margin: 20px auto !important;
            }
            body.article-content::after { content: ""; display: table; clear: both; }
            body.article-content figcaption {
                font-family: "Playfair Display", Georgia, serif;
                font-size: 0.75rem;
                font-variant: small-caps;
                letter-spacing: 1px;
                color: #6b5a3e;
                text-align: center;
                border: 1px solid #8b7550;
                padding: 4px 8px;
            }
        `,
        branding: false,
        promotion: false,
        image_dimensions: true,
        image_advtab: true,
        image_class_list: [
            { title: 'None (full width)', value: '' },
            { title: 'Align Left — wrap text right', value: 'alignleft' },
            { title: 'Align Right — wrap text left', value: 'alignright' },
            { title: 'Center — no wrap', value: 'aligncenter' }
        ],
        images_upload_url: '<?php echo SITE_URL; ?>/api/upload.php',
        images_upload_credentials: true,
        images_upload_handler: function(blobInfo, progress) {
            return new Promise(function(resolve, reject) {
                var xhr = new XMLHttpRequest();
                xhr.open('POST', '<?php echo SITE_URL; ?>/api/upload.php');
                xhr.withCredentials = true;
                xhr.upload.onprogress = function(e) {
                    progress(e.loaded / e.total * 100);
                };
                xhr.onload = function() {
                    var json;
                    try {
                        json = JSON.parse(xhr.responseText);
                    } catch (e) {
                        reject('Invalid JSON response');
                        return;
                    }
                    if (xhr.status === 403) {
                        reject('CSRF error');
                        return;
                    }
                    if (!json || !json.file) {
                        reject('Upload failed: ' + xhr.responseText);
                        return;
                    }
                    resolve(json.file);
                };
                xhr.onerror = function() {
                    reject('Upload failed');
                };
                var formData = new FormData();
                formData.append('file', blobInfo.blob(), blobInfo.filename());
                formData.append('csrf_token', document.querySelector('input[name=csrf_token]').value);
                xhr.send(formData);
            });
        },
        setup: function(editor) {
            editor.on('NodeChange', function(e) {
                if (e.element.tagName === 'IMG') {
                    var src = e.element.getAttribute('src');
                    if (src && (src.startsWith('C:\\') || src.startsWith('file://'))) {
                        editor.notificationManager.open({
                            text: 'Local file path detected! Please use the Upload tab to insert images.',
                            type: 'warning',
                            timeout: 5000
                        });
                    }
                }
            });
        }
    });
</script>
<script>
    function selectMedia(id, el) {
        document.getElementById('selected_media_id').value = id;
        document.querySelectorAll('.media-item').forEach(i => i.classList.remove('selected'));
        el.classList.add('selected');
    }
</script>
</body>

</html>
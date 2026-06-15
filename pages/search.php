<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/helpers.php';
require_once __DIR__ . '/../functions/validation.php';

$query       = isset($_GET['q'])           ? Validate::sanitize($_GET['q'])           : '';
$dateFrom    = isset($_GET['date_from'])   ? Validate::sanitize($_GET['date_from'])   : '';
$dateTo      = isset($_GET['date_to'])     ? Validate::sanitize($_GET['date_to'])     : '';
$authorF     = isset($_GET['author'])      ? Validate::sanitize($_GET['author'])      : '';
$categoryId  = isset($_GET['category_id']) ? (int)$_GET['category_id']               : 0;
$sort        = isset($_GET['sort']) && in_array($_GET['sort'], ['relevance','date_desc','date_asc'])
               ? $_GET['sort'] : 'relevance';

$filters = array_filter([
    'date_from'   => $dateFrom,
    'date_to'     => $dateTo,
    'author'      => $authorF,
    'category_id' => $categoryId ?: null,
    'sort'        => $sort,
]);

$articles   = [];
$searched   = (!empty($query) || !empty($dateFrom) || !empty($dateTo) || !empty($authorF) || !empty($categoryId));
if ($searched) {
    $articles = DB::searchArticles($query, $filters);
}

$authors    = DB::getPublishedAuthors();
$categories = DB::getCategories();

$pageTitle = 'Search Articles';
include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<style>
/* ── Search page styles ── */
.search-wrap { max-width: 860px; margin: 2rem auto; padding: 0 1rem; }

/* Search box + suggestions */
.search-box-wrap { position: relative; }
.search-box-wrap input[type=text] { width: 100%; padding: .6rem 1rem; font-size: 1rem; border: 2px solid #8b6914; background: #fdf6e3; color: #2c1a00; border-radius: 3px; box-sizing: border-box; }
#search-suggestions { position: absolute; top: 100%; left: 0; right: 0; background: #fdf6e3; border: 1px solid #c9a84c; border-top: none; z-index: 100; list-style: none; margin: 0; padding: 0; box-shadow: 0 4px 8px rgba(0,0,0,.15); }
#search-suggestions li { padding: .55rem 1rem; cursor: pointer; border-bottom: 1px solid #efe0b0; }
#search-suggestions li:hover, #search-suggestions li.active { background: #f5e6b0; }
#search-suggestions li .sug-category { font-size: .75rem; color: #7a5c10; margin-left: .4rem; }
#search-suggestions li .sug-author  { font-size: .75rem; color: #999; margin-left: .3rem; }

/* Filters panel */
.filters-panel { background: #fdf0cb; border: 1px solid #c9a84c; border-radius: 3px; padding: 1rem; margin: 1rem 0; }
.filters-panel legend { font-weight: bold; color: #5a3e00; margin-bottom: .6rem; display: block; }
.filters-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: .7rem; }
.filters-grid label { font-size: .85rem; color: #4a3000; }
.filters-grid select,
.filters-grid input[type=date] { width: 100%; padding: .4rem .6rem; border: 1px solid #c9a84c; background: #fffdf5; border-radius: 3px; font-size: .85rem; box-sizing: border-box; }
.filter-actions { margin-top: .8rem; display: flex; gap: .5rem; flex-wrap: wrap; }
.btn { background: #8b6914; color: #fff; border: none; padding: .5rem 1.2rem; cursor: pointer; border-radius: 3px; font-size: .9rem; }
.btn:hover { background: #6b4f0e; }
.btn-ghost { background: transparent; border: 1px solid #8b6914; color: #8b6914; }
.btn-ghost:hover { background: #f5e6b0; }

/* Results */
.result-count { color: #666; font-size: .9rem; margin: .8rem 0; }
.article-item { display: flex; gap: 1rem; border-bottom: 1px solid #ddd; padding: 1rem 0; }
.article-item:last-child { border-bottom: none; }
.article-info { flex: 1; }
.article-info h3 { margin: .3rem 0; }
.article-info h3 a { color: #5a3e00; text-decoration: none; }
.article-info h3 a:hover { text-decoration: underline; }
.article-thumb img { width: 110px; height: 80px; object-fit: cover; border-radius: 3px; }
.meta { font-size: .8rem; color: #888; margin-top: .4rem; }
.meta span + span::before { content: ' · '; }
.category-badge { font-size: .75rem; background: #8b6914; color: #fff; padding: 1px 7px; border-radius: 10px; }
.no-results { padding: 2rem; text-align: center; color: #888; }
</style>

<main class="main-content">
<div class="search-wrap">
    <h1>Search Articles</h1>

    <?php if (!$searched): ?>
    <form method="GET" action="" id="search-form" autocomplete="off">

        <div class="search-box-wrap">
            <input type="text" name="q" id="search-input"
                   placeholder="Search articles…"
                   value="<?php echo htmlspecialchars($query); ?>">
            <ul id="search-suggestions" hidden></ul>
        </div>

        <details <?php if ($dateFrom||$dateTo||$authorF||$categoryId) echo 'open'; ?> style="margin-top:.7rem">
            <summary style="cursor:pointer;color:#8b6914;font-size:.9rem;">▾ Filters</summary>
            <div class="filters-panel" style="margin-top:.5rem">
                <div class="filters-grid">
                    <div>
                        <label>Date From<br>
                            <input type="date" name="date_from" value="<?php echo htmlspecialchars($dateFrom); ?>">
                        </label>
                    </div>
                    <div>
                        <label>Date To<br>
                            <input type="date" name="date_to" value="<?php echo htmlspecialchars($dateTo); ?>">
                        </label>
                    </div>
                    <div>
                        <label>Author<br>
                            <select name="author">
                                <option value="">All Authors</option>
                                <?php foreach ($authors as $a): ?>
                                <option value="<?php echo htmlspecialchars($a['username']); ?>"
                                    <?php if ($authorF === $a['username']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($a['username']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div>
                        <label>Category<br>
                            <select name="category_id">
                                <option value="">All Categories</option>
                                <?php foreach ($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"
                                    <?php if ($categoryId == $cat['id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </label>
                    </div>
                    <div>
                        <label>Sort By<br>
                            <select name="sort">
                                <option value="relevance" <?php if ($sort==='relevance') echo 'selected'; ?>>Relevance</option>
                                <option value="date_desc" <?php if ($sort==='date_desc') echo 'selected'; ?>>Newest First</option>
                                <option value="date_asc"  <?php if ($sort==='date_asc')  echo 'selected'; ?>>Oldest First</option>
                            </select>
                        </label>
                    </div>
                </div>
                <div class="filter-actions">
                    <button type="submit" class="btn">Apply Filters</button>
                    <a href="search.php" class="btn btn-ghost">Reset</a>
                </div>
            </div>
        </details>

    </form>
    <?php endif; ?>

    <?php if ($searched): ?>
        <p class="result-count">
            <?php echo count($articles); ?> result(s)
            <?php if ($query) echo 'for "<strong>' . htmlspecialchars($query) . '</strong>"'; ?>
            &nbsp;&middot;&nbsp; <a href="search.php" style="color:#8b6914;font-size:0.85rem;">&larr; New Search</a>
        </p>

        <?php if (count($articles) > 0): ?>
            <section class="articles-list">
                <?php foreach ($articles as $article): ?>
                    <article class="article-item">
                        <div class="article-info">
                            <span class="category-badge"><?php echo htmlspecialchars($article['category_name'] ?? ''); ?></span>
                            <h3><a href="<?php echo SITE_URL; ?>/pages/article.php?id=<?php echo $article['id']; ?>">
                                <?php echo htmlspecialchars($article['title']); ?>
                            </a></h3>
                            <p><?php echo htmlspecialchars(substr($article['excerpt'] ?? '', 0, 160)); ?></p>
                            <div class="meta">
                                <span><?php echo htmlspecialchars($article['author'] ?? ''); ?></span>
                                <span><?php echo Helper::formatDate($article['created_at']); ?></span>
                            </div>
                        </div>
                        <?php if (!empty($article['image'])): ?>
                            <div class="article-thumb">
                                <img src="<?php echo SITE_URL; ?>/uploads/articles/<?php echo $article['image']; ?>"
                                     alt="<?php echo htmlspecialchars($article['title']); ?>">
                            </div>
                        <?php endif; ?>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php else: ?>
            <div class="no-results"><p>No articles found. Try different keywords or filters.</p></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</main>

<script>
(function () {
    const input = document.getElementById('search-input');
    const list  = document.getElementById('search-suggestions');
    const SITE  = '<?php echo SITE_URL; ?>';
    let timer, activeIdx = -1;

    function hideSuggestions() {
        list.hidden = true;
        list.innerHTML = '';
        activeIdx = -1;
    }

    function showSuggestions(items) {
        list.innerHTML = '';
        if (!items.length) { hideSuggestions(); return; }

        items.forEach((item, i) => {
            const li = document.createElement('li');
            li.innerHTML =
                '<span class="sug-title">' + escHtml(item.title) + '</span>' +
                (item.category_name ? '<span class="sug-category">[' + escHtml(item.category_name) + ']</span>' : '') +
                (item.author        ? '<span class="sug-author">— ' + escHtml(item.author) + '</span>' : '');
            li.addEventListener('mousedown', (e) => {
                e.preventDefault();
                window.location.href = SITE + '/pages/article.php?id=' + item.id;
            });
            list.appendChild(li);
        });
        list.hidden = false;
    }

    function setActive(idx) {
        const items = list.querySelectorAll('li');
        items.forEach(li => li.classList.remove('active'));
        activeIdx = idx;
        if (idx >= 0 && idx < items.length) items[idx].classList.add('active');
    }

    function escHtml(s) {
        return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
    }

    input.addEventListener('input', () => {
        const q = input.value.trim();
        clearTimeout(timer);
        if (q.length < 2) { hideSuggestions(); return; }
        timer = setTimeout(() => {
            fetch(SITE + '/api/search.php?mode=suggest&q=' + encodeURIComponent(q))
                .then(r => r.json())
                .then(data => {
                    if (data.success) showSuggestions(data.suggestions);
                })
                .catch(() => {});
        }, 250); // debounce 250ms
    });

    input.addEventListener('keydown', (e) => {
        const items = list.querySelectorAll('li');
        if (e.key === 'ArrowDown')  { e.preventDefault(); setActive(Math.min(activeIdx + 1, items.length - 1)); }
        if (e.key === 'ArrowUp')    { e.preventDefault(); setActive(Math.max(activeIdx - 1, 0)); }
        if (e.key === 'Escape')     { hideSuggestions(); }
        if (e.key === 'Enter' && activeIdx >= 0) {
            e.preventDefault();
            items[activeIdx].dispatchEvent(new MouseEvent('mousedown'));
        }
    });

    document.addEventListener('click', (e) => {
        if (!input.contains(e.target) && !list.contains(e.target)) hideSuggestions();
    });
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
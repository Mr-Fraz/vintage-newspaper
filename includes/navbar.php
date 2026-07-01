<?php
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/auth.php';

$categories  = DB::getCategories();
$authors     = DB::getPublishedAuthors();
?>

<!-- Victorian Masthead -->
<header class="masthead">
    <div class="masthead-pub-info">
        <span>Est. <?php echo date('Y'); ?></span>
        <span><?php echo strtoupper(date('l, F j, Y')); ?></span>

        <!-- Inline search + filter — no page redirect -->
        <div class="masthead-search-wrap">
            <div class="masthead-search">
                <input type="text" id="ms-input" placeholder="Search articles…"
                       autocomplete="off" aria-label="Search articles">
                <button type="button" id="ms-search-btn" aria-label="Search">
                    <i data-lucide="search" style="width:13px;height:13px;vertical-align:middle;"></i>
                </button>
                <button type="button" id="ms-filter-btn" aria-label="Filters" title="Filters">
                    <i data-lucide="sliders-horizontal" style="width:13px;height:13px;vertical-align:middle;"></i>
                </button>
            </div>

            <!-- Live suggestions -->
            <ul id="ms-suggestions" class="ms-suggestions" hidden></ul>

            <!-- Filter panel dropdown -->
            <div id="ms-filter-panel" class="ms-filter-panel" hidden>
                <div class="ms-filter-grid">
                    <label>Category
                        <select id="mf-cat">
                            <option value="">All</option>
                            <?php foreach ($categories as $c): ?>
                            <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>Author
                        <select id="mf-author">
                            <option value="">All</option>
                            <?php foreach ($authors as $a): ?>
                            <option value="<?php echo htmlspecialchars($a['username']); ?>"><?php echo htmlspecialchars($a['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>
                    <label>From <input type="date" id="mf-from"></label>
                    <label>To   <input type="date" id="mf-to"></label>
                    <label>Sort
                        <select id="mf-sort">
                            <option value="relevance">Relevance</option>
                            <option value="date_desc">Newest</option>
                            <option value="date_asc">Oldest</option>
                        </select>
                    </label>
                </div>
                <div class="ms-filter-actions">
                    <button type="button" id="mf-apply" class="btn btn-primary" style="font-size:0.65rem;padding:6px 14px;">Apply</button>
                    <button type="button" id="mf-reset" class="btn" style="font-size:0.65rem;padding:6px 14px;">Reset</button>
                </div>
            </div>
        </div>
    </div>

    <h1 class="masthead-title"><?php echo SITE_NAME; ?></h1>
    <p class="masthead-tagline">Your Most Faithful Daily Chronicle of Truth &amp; Intelligence</p>

    <div class="ornamental-rule">
        <div class="orn-center">
            <span class="orn-line"></span><span class="orn-line thin"></span>
            <span class="orn-icon">❧</span>
            <span class="orn-line thin"></span><span class="orn-line"></span>
        </div>
    </div>
    <hr class="masthead-rule">
</header>

<div class="date-strip">
    <?php
    $cat_names = array_column($categories, 'name');
    echo implode(' &nbsp;·&nbsp; ', array_map('strtoupper', array_slice($cat_names, 0, 5)));
    ?>
</div>

<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>"><h1><?php echo SITE_NAME; ?></h1></a>
        </div>

        <ul class="nav-links" id="nav-menu">
            <button type="button" class="nav-links-close" id="nav-close-btn" aria-label="Close menu">
                <i data-lucide="x" style="width:16px;height:16px;"></i>
            </button>
            <li><a href="<?php echo SITE_URL; ?>"><i data-lucide="home"></i> Home</a></li>

            <?php if (!empty($categories)): ?>
            <li class="has-dropdown">
                <a href="#" class="dropdown-toggle" aria-haspopup="true">
                    <i data-lucide="layout-grid"></i> Categories <span class="dropdown-arrow">▾</span>
                </a>
                <ul class="dropdown-menu">
                    <button type="button" class="dropdown-menu-close" id="cat-close-btn" aria-label="Close categories">
                        <i data-lucide="x" style="width:16px;height:16px;"></i>
                    </button>
                    <div class="dropdown-menu-inner">
                    <li class="dropdown-menu-back" id="cat-back-btn">
                        <i data-lucide="chevron-left" style="width:14px;height:14px;"></i> Back
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li><a href="<?php echo SITE_URL; ?>/pages/category.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a></li>
                    <?php endforeach; ?>
                    </div>
                </ul>
            </li>
            <?php endif; ?>

            <?php if (Auth::isLoggedIn()): ?>
                <?php if (in_array($_SESSION['role'], ['admin', 'editor'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/admin/"><i data-lucide="layout-dashboard"></i> Dashboard</a></li>
                <?php endif; ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/profile.php"><i data-lucide="user-circle"></i> My Profile</a></li>
                <li class="nav-login"><a href="<?php echo SITE_URL; ?>/admin/logout.php"><i data-lucide="log-out"></i> Logout</a></li>
            <?php else: ?>
                <li class="nav-login nav-auth">
                    <a href="<?php echo SITE_URL; ?>/pages/login.php"><i data-lucide="log-in"></i> Login</a><span class="nav-auth-sep">/</span><a href="<?php echo SITE_URL; ?>/pages/register.php">Sign Up</a>
                </li>
            <?php endif; ?>
        </ul>

        <button type="button" class="hamburger" id="menu-toggle" aria-label="Open menu" aria-expanded="false" aria-controls="nav-menu">
            <span></span><span></span><span></span>
        </button>
    </div>
    <div class="nav-links-backdrop" id="nav-backdrop"></div>
</nav>

<script>
(function () {
    const SITE = '<?php echo SITE_URL; ?>';
    const input     = document.getElementById('ms-input');
    const searchBtn = document.getElementById('ms-search-btn');
    const filterBtn = document.getElementById('ms-filter-btn');
    const panel     = document.getElementById('ms-filter-panel');
    const suggs     = document.getElementById('ms-suggestions');
    const applyBtn  = document.getElementById('mf-apply');
    const resetBtn  = document.getElementById('mf-reset');

    let timer, activeIdx = -1;

    // ── Filter panel toggle ──────────────────────────────
    filterBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        panel.hidden = !panel.hidden;
        suggs.hidden = true;
        filterBtn.classList.toggle('active', !panel.hidden);

        if (!panel.hidden && window.innerWidth <= 768) {
            const btnRect = filterBtn.getBoundingClientRect();
            panel.style.top = (btnRect.bottom + 8) + 'px';
        } else {
            panel.style.top = '';
        }
    });

    // ── Build search URL and navigate ───────────────────
    function goSearch() {
        const q = input.value.trim();
        const cat    = document.getElementById('mf-cat').value;
        const author = document.getElementById('mf-author').value;
        const from   = document.getElementById('mf-from').value;
        const to     = document.getElementById('mf-to').value;
        const sort   = document.getElementById('mf-sort').value;

        const params = new URLSearchParams();
        if (q)      params.set('q', q);
        if (cat)    params.set('category_id', cat);
        if (author) params.set('author', author);
        if (from)   params.set('date_from', from);
        if (to)     params.set('date_to', to);
        if (sort && sort !== 'relevance') params.set('sort', sort);

        window.location.href = SITE + '/pages/search.php?' + params.toString();
    }

    searchBtn.addEventListener('click', goSearch);
    applyBtn.addEventListener('click', goSearch);

    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') { e.preventDefault(); goSearch(); }
    });

    resetBtn.addEventListener('click', function () {
        document.getElementById('mf-cat').value    = '';
        document.getElementById('mf-author').value = '';
        document.getElementById('mf-from').value   = '';
        document.getElementById('mf-to').value     = '';
        document.getElementById('mf-sort').value   = 'relevance';
    });

    // ── Live suggestions ─────────────────────────────────
    function hideSuggs() { suggs.hidden = true; suggs.innerHTML = ''; activeIdx = -1; }

    input.addEventListener('input', function () {
        const q = input.value.trim();
        clearTimeout(timer);
        if (q.length < 2) { hideSuggs(); return; }
        timer = setTimeout(function () {
            fetch(SITE + '/api/search.php?mode=suggest&q=' + encodeURIComponent(q))
                .then(function (r) { return r.json(); })
                .then(function (data) {
                    if (!data.success || !data.suggestions.length) { hideSuggs(); return; }
                    suggs.innerHTML = '';
                    data.suggestions.forEach(function (item) {
                        const li = document.createElement('li');
                        li.innerHTML = '<span class="ms-sug-title">' + esc(item.title) + '</span>'
                            + (item.category_name ? '<span class="ms-sug-cat">' + esc(item.category_name) + '</span>' : '');
                        li.addEventListener('mousedown', function (e) {
                            e.preventDefault();
                            window.location.href = SITE + '/pages/article.php?id=' + item.id;
                        });
                        suggs.appendChild(li);
                    });
                    suggs.hidden = false;
                })
                .catch(function () {});
        }, 250);
    });

    input.addEventListener('keydown', function (e) {
        const items = suggs.querySelectorAll('li');
        if (e.key === 'ArrowDown')  { e.preventDefault(); setActive(Math.min(activeIdx + 1, items.length - 1)); }
        if (e.key === 'ArrowUp')    { e.preventDefault(); setActive(Math.max(activeIdx - 1, 0)); }
        if (e.key === 'Escape')     { hideSuggs(); }
    });

    function setActive(idx) {
        suggs.querySelectorAll('li').forEach(function (li) { li.classList.remove('active'); });
        activeIdx = idx;
        if (idx >= 0) suggs.querySelectorAll('li')[idx].classList.add('active');
    }

    function esc(s) { return s.replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }

    // ── Close on outside click ───────────────────────────
    document.addEventListener('click', function (e) {
        const wrap = document.querySelector('.masthead-search-wrap');
        if (wrap && !wrap.contains(e.target)) { hideSuggs(); panel.hidden = true; filterBtn.classList.remove('active'); }
    });

    // ── Reliable Lucide icon init (handles load-order race) ──
    function initIcons() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        } else {
            setTimeout(initIcons, 50);
        }
    }
    initIcons();
})();
</script>
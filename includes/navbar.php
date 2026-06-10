<?php
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/auth.php';

$categories = DB::getCategories();
?>

<!-- Victorian Masthead -->
<header class="masthead">
    <div class="masthead-pub-info">
        <span>Est. <?php echo date('Y'); ?></span>
        <span><?php echo strtoupper(date('l, F j, Y')); ?></span>
        <span>Price: One Penny</span>
    </div>

    <h1 class="masthead-title"><?php echo SITE_NAME; ?></h1>
    <p class="masthead-tagline">Your Most Faithful Daily Chronicle of Truth &amp; Intelligence</p>

    <!-- Ornamental Rule -->
    <div class="ornamental-rule">
        <div class="orn-center">
            <span class="orn-line"></span>
            <span class="orn-line thin"></span>
            <span class="orn-icon">❧</span>
            <span class="orn-line thin"></span>
            <span class="orn-line"></span>
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
            <a href="<?php echo SITE_URL; ?>">
                <h1><?php echo SITE_NAME; ?></h1>
            </a>
        </div>

        <ul class="nav-links">
            <li><a href="<?php echo SITE_URL; ?>">Home</a></li>

            <?php foreach ($categories as $cat): ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/category.php?slug=<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></a></li>
            <?php endforeach; ?>

            <li><a href="<?php echo SITE_URL; ?>/pages/search.php">Search</a></li>

            <?php if (Auth::isLoggedIn()): ?>
             	<?php if (in_array($_SESSION['role'], ['admin', 'editor'])): ?>
                <li><a href="<?php echo SITE_URL; ?>/admin/">Dashboard</a></li>
            <?php endif; ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/profile.php">My Profile</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
        
        <!-- Dark Mode Toggle
        <button class="dark-toggle" id="dark-toggle-btn" aria-label="Switch to dark mode" title="Switch to dark mode">
            <span class="toggle-icon" id="toggle-icon">🌙</span>
            <span class="toggle-label" id="toggle-label">Dark</span>
        </button>  -->

        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>
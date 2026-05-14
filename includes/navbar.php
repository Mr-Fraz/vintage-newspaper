<?php
require_once __DIR__ . '/../functions/db.php';
require_once __DIR__ . '/../functions/auth.php';

$categories = DB::getCategories();
?>

<nav class="navbar">
    <div class="container">
        <div class="logo">
            <a href="<?php echo SITE_URL; ?>">
                <h1><?php echo SITE_NAME; ?></h1>
                <p class="tagline">Your Daily Source of Truth</p>
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
                <li><a href="<?php echo SITE_URL; ?>/admin/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
        
        <!-- Dark Mode Toggle -->
        <button class="dark-toggle" id="dark-toggle-btn" aria-label="Switch to dark mode" title="Switch to dark mode">
            <span class="toggle-icon" id="toggle-icon">🌙</span>
            <span class="toggle-label" id="toggle-label">Dark</span>
        </button> 

        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>
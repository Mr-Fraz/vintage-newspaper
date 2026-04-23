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
                <li><a href="<?php echo SITE_URL; ?>/category/<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></a></li>
            <?php endforeach; ?>
            
            <li><a href="<?php echo SITE_URL; ?>/pages/search.php">Search</a></li>
            
            <?php if (Auth::isLoggedIn()): ?>
                <li><a href="<?php echo SITE_URL; ?>/admin/">Dashboard</a></li>
                <li><a href="<?php echo SITE_URL; ?>/admin/logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="<?php echo SITE_URL; ?>/pages/login.php">Login</a></li>
            <?php endif; ?>
        </ul>
        
        <div class="hamburger">
            <span></span>
            <span></span>
            <span></span>
        </div>
    </div>
</nav>

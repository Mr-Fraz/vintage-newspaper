<footer class="footer">
        <div class="container">

            <!-- DESKTOP: 3-section layout -->
            <div class="footer-content footer-desktop">
                <div class="footer-section">
                    <h3><?php echo SITE_NAME; ?></h3>
                    <p>Delivering quality news since 2024</p>
                </div>
                <div class="footer-section">
                    <h4>Quick Links</h4>
                    <ul>
                        <li><a href="<?php echo SITE_URL; ?>">Home</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/about.php">About</a></li>
                        <li><a href="<?php echo SITE_URL; ?>/pages/contact.php">Contact</a></li>
                    </ul>
                </div>
                <div class="footer-section">
                    <h4>Categories</h4>
                    <ul>
                        <?php
                        $categories = DB::getCategories();
                        foreach (array_slice($categories, 0, 4) as $cat):
                        ?>
                        <li><a href="<?php echo SITE_URL; ?>/pages/category.php?slug=<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom footer-desktop">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>

            <!-- MOBILE: 2-section compact bar -->
            <div class="footer-mobile">
                <div class="footer-mobile-row">
                    <div class="footer-mobile-col">
                        <span class="footer-mobile-head">Navigate</span>
                        <a href="<?php echo SITE_URL; ?>">Home</a>
                        <a href="<?php echo SITE_URL; ?>/pages/about.php">About</a>
                        <a href="<?php echo SITE_URL; ?>/pages/contact.php">Contact</a>
                    </div>
                    <div class="footer-mobile-col">
                        <span class="footer-mobile-head">Categories</span>
                        <?php foreach (array_slice($categories, 0, 4) as $cat): ?>
                        <a href="<?php echo SITE_URL; ?>/pages/category.php?slug=<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></a>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="footer-mobile-copy">&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?></div>
            </div>

        </div>
    </footer>

    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>
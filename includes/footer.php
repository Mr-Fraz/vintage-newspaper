    <footer class="footer">
        <div class="container">
            <div class="footer-content">
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
                            <li><a href="<?php echo SITE_URL; ?>/category/<?php echo $cat['slug']; ?>"><?php echo $cat['name']; ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
            
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. All rights reserved.</p>
            </div>
        </div>
    </footer>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    <?php if (isset($extraJS)) echo $extraJS; ?>
</body>
</html>

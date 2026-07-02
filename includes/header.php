<?php
if (!defined('SITE_NAME')) {
    require_once __DIR__ . '/../config/config.php';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?><?php echo SITE_NAME; ?></title>
    <?php if (!empty($metaDescription)): ?>
    <meta name="description" content="<?php echo htmlspecialchars(strip_tags($metaDescription)); ?>">
    <?php endif; ?>
    <?php if (!empty($canonicalUrl)): ?>
    <link rel="canonical" href="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <?php endif; ?>
    <meta property="og:site_name" content="<?php echo SITE_NAME; ?>">
    <meta property="og:title" content="<?php echo htmlspecialchars(isset($pageTitle) ? $pageTitle : SITE_NAME); ?>">
    <?php if (!empty($metaDescription)): ?>
    <meta property="og:description" content="<?php echo htmlspecialchars(strip_tags($metaDescription)); ?>">
    <?php endif; ?>
    <?php if (!empty($ogImage)): ?>
    <meta property="og:image" content="<?php echo htmlspecialchars($ogImage); ?>">
    <?php endif; ?>
    <meta property="og:type" content="<?php echo !empty($canonicalUrl) ? 'article' : 'website'; ?>">
    <?php if (!empty($canonicalUrl)): ?>
    <meta property="og:url" content="<?php echo htmlspecialchars($canonicalUrl); ?>">
    <?php endif; ?>
    <meta name="twitter:card" content="<?php echo !empty($ogImage) ? 'summary_large_image' : 'summary'; ?>">
    <script>
        window.SITE_URL = '<?php echo SITE_URL; ?>';
        if (localStorage.getItem('vn_dark_mode') === 'dark') {
            document.documentElement.classList.add('dark-mode-preload');
        }
    </script>
    <style>
        .dark-mode-preload body {
            background: #1a1410 !important;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
   <link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;700;800;900&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,600;1,8..60,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>
    <script>
      window.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
          if (typeof lucide === 'undefined') {
            var fallback = document.createElement('script');
            fallback.src = 'https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js';
            fallback.onload = function () { if (typeof lucide !== 'undefined') lucide.createIcons(); };
            document.head.appendChild(fallback);
          }
        }, 800);
      });
    </script>
    <!-- <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/dark-mode.css"> -->
    <?php if (isset($extraCSS)) echo $extraCSS; ?>
</head>

<body>
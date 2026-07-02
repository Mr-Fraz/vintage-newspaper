<?php
require_once __DIR__ . '/../../config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' : ''; ?>Admin Panel</title>
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/style.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css"> 
    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/6.8.5/tinymce.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/lucide@0.469.0/dist/umd/lucide.min.js" defer></script>
    <script>
      window.addEventListener('DOMContentLoaded', function () {
        setTimeout(function () {
          if (typeof lucide === 'undefined') {
            var fallback = document.createElement('script');
            fallback.src = 'https://unpkg.com/lucide@0.469.0/dist/umd/lucide.min.js';
            fallback.onload = function () { if (typeof lucide !== 'undefined') lucide.createIcons(); };
            document.head.appendChild(fallback);
          } else {
            lucide.createIcons();
          }
        }, 300);
      });
    </script>
</head>
<body class="admin-body">
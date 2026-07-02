<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
$pageTitle = 'About';
$extraCSS = '<link rel="stylesheet" href="' . SITE_URL . '/assets/css/pages.css">';

$totalArticles = DB::countArticles();
$totalCategories = count(DB::getCategories());

include __DIR__ . '/../includes/header.php';
include __DIR__ . '/../includes/navbar.php';
?>

<main class="main-content">
  <div class="container">
    <div class="page-wrapper">

      <a href="<?php echo SITE_URL; ?>" class="back-link">Return to Front Page</a>

      <div class="page-masthead">
        <span class="site-name"><?php echo SITE_NAME; ?></span>
        <div class="meta-bar">
          <span>Vol. I — No. 001</span>
          <span><?php echo strtoupper(date('l, F j, Y')); ?></span>
          <span>Price: One Penny</span>
        </div>
      </div>

      <!-- Stats strip -->
      <div class="about-stats">
        <div class="about-stat">
          <i data-lucide="newspaper"></i>
          <span class="about-stat-num"><?php echo $totalArticles; ?></span>
          <span class="about-stat-label">Dispatches Published</span>
        </div>
        <div class="about-stat">
          <i data-lucide="layout-grid"></i>
          <span class="about-stat-num"><?php echo $totalCategories; ?></span>
          <span class="about-stat-label">Categories Covered</span>
        </div>
        <div class="about-stat">
          <i data-lucide="calendar"></i>
          <span class="about-stat-num"><?php echo date('Y'); ?></span>
          <span class="about-stat-label">Est. &amp; Running</span>
        </div>
      </div>

      <div class="section-head">About This Gazette</div>

      <div class="content-grid">
        <div class="content-main">
          <div class="article-body">
            <h2>Our Mission: Preserving the Printed Word</h2>
            <p>This publication serves as a living archive of historical aesthetic. In an age of rapid pixels and fleeting data, the Vintage Gazette bridges the gap between modern technology and the timeless beauty of 19th-century newsprint. Each layout, font choice, and digital ink mark is meticulously crafted to transport the reader back to an era where information carried weight — literally and figuratively.</p>
            <p>Through this project, we explore the intersection of classical typography and modern web development, ensuring that the soul of the broadsheet remains alive in the digital frontier. Our commitment to quality journalism mirrors the dedication of Victorian-era pressmen who set type by hand and pulled each impression with care.</p>
            <p>The Vintage Gazette covers breaking news, business dispatches, entertainment chronicles, sporting results, and technological marvels — all presented with the gravitas befitting a publication of the highest order.</p>
          </div>
        </div>

        <div class="content-sidebar">
          <div class="sidebar-box">
            <h3><i data-lucide="feather"></i> The Editor's Note</h3>
            <p>Welcome to our chronicle. This project is a testament to the power of design to evoke nostalgia and tell a story beyond the text itself.</p>
            <p><strong>Lead Designer:</strong> Muhammad Faraz<br>
               <strong>Technology:</strong> PHP, CSS3, Apache<br>
               <strong>Est.</strong> <?php echo date('Y'); ?></p>
          </div>
          <div class="sidebar-box">
            <h3><i data-lucide="scroll"></i> Our Principles</h3>
            <ul>
              <li><i data-lucide="check"></i> Truth in every dispatch</li>
              <li><i data-lucide="check"></i> Clarity of expression</li>
              <li><i data-lucide="check"></i> Fidelity to the reader</li>
              <li><i data-lucide="check"></i> Excellence in craft</li>
            </ul>
          </div>
        </div>
      </div>

      <div class="page-footer-note">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; All Rights Reserved
      </div>

    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
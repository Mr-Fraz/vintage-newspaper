<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../functions/db.php';
$pageTitle = 'Contact';
$extraCSS = '<link rel="stylesheet" href="' . SITE_URL . '/assets/css/pages.css">';
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
          <span>Vol. I — No. 002</span>
          <span><?php echo strtoupper(date('l, F j, Y')); ?></span>
          <span>Classifieds Section</span>
        </div>
      </div>

      <div class="section-head">Official Directory &amp; Inquiries</div>

      <div class="contact-grid">

        <div class="contact-col">
          <h2>Digital Dispatches</h2>
          <div class="contact-method">
            <span class="contact-label">Electronic Mail</span>
            <span class="contact-value"><a href="mailto:vintagepress@example.com">vintagepress@example.com</a></span>
          </div>
          <div class="contact-method">
            <span class="contact-label">World Wide Web</span>
            <span class="contact-value"><a href="<?php echo SITE_URL; ?>" target="_blank"><?php echo SITE_NAME; ?></a></span>
          </div>
          <div class="contact-method">
            <span class="contact-label">LinkedIn Profile</span>
            <span class="contact-value"><a href="https://linkedin.com/in/yourprofile" target="_blank">linkedin.com/vintagenews</a></span>
          </div>
        </div>

        <div class="contact-col">
          <h2>Post &amp; Telegraph</h2>
          <div class="contact-method">
            <span class="contact-label">Physical Press Office</span>
            <span class="contact-value">123 Press Pass Lane<br>Inkwell City, IC 54321</span>
          </div>
          <div class="contact-method">
            <span class="contact-label">Telephone Exchange</span>
            <span class="contact-value">KLondike 5-0199</span>
          </div>
          <div class="contact-method">
            <span class="contact-label">Telegraph Code</span>
            <span class="contact-value">VINTAGEPRESS-XYZ</span>
          </div>
        </div>

      </div>

      <div class="cta-box">
        <h3>"Stop the Presses!"</h3>
        <p>Seeking a designer versed in the historical arts? Have an inquiry regarding a digital archive project? The Gazette is currently accepting new commissions and collaborations.</p>
        <p><strong>Dispatch your inquiry today via the electronic mail channel listed above.</strong></p>
      </div>

      <div class="page-footer-note">
        &copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> &mdash; All Rights Reserved
      </div>

    </div>
  </div>
</main>

<?php include __DIR__ . '/../includes/footer.php'; ?>
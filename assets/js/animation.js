// ============================================
// VINTAGE NEWSPAPER — animations.js
// Include before </body> on all pages
// ============================================

document.addEventListener('DOMContentLoaded', function () {

  // ── Hamburger toggle ──
  const hamburger = document.querySelector('.hamburger');
  const navLinks  = document.querySelector('.nav-links');
  if (hamburger && navLinks) {
    hamburger.addEventListener('click', function () {
      hamburger.classList.toggle('active');
      navLinks.classList.toggle('active');
    });
    // Close on nav link click (mobile)
    navLinks.querySelectorAll('a').forEach(function (link) {
      link.addEventListener('click', function () {
        hamburger.classList.remove('active');
        navLinks.classList.remove('active');
      });
    });
  }

  // ── Scroll Reveal ──
  const revealEls = document.querySelectorAll(
    '.article-card, .article-item, .hero, .comments-section, .stat-card, .admin-section'
  );

  if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08 });

    revealEls.forEach(function (el) {
      el.classList.add('reveal');
      observer.observe(el);
    });
  } else {
    // Fallback: just show everything
    revealEls.forEach(function (el) { el.classList.add('visible'); });
  }

  // ── Page navigation fade ──
  // Smooth fade-out on internal link click
  document.querySelectorAll('a[href]').forEach(function (link) {
    const href = link.getAttribute('href');
    // Only internal, non-anchor links
    if (
      href &&
      !href.startsWith('#') &&
      !href.startsWith('http') &&
      !href.startsWith('mailto') &&
      !href.startsWith('javascript') &&
      link.target !== '_blank'
    ) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        document.body.style.transition = 'opacity 0.25s ease';
        document.body.style.opacity   = '0';
        setTimeout(function () {
          window.location.href = href;
        }, 240);
      });
    }
  });

  // ── Active nav link highlight ──
  const currentPath = window.location.pathname;
  document.querySelectorAll('.nav-links a, .admin-nav a').forEach(function (link) {
    if (link.getAttribute('href') && currentPath.endsWith(link.getAttribute('href'))) {
      link.classList.add('active');
    }
  });

  // ── Stagger article cards ──
  document.querySelectorAll('.grid .article-card').forEach(function (card, i) {
    card.style.animationDelay = (i * 0.06) + 's';
  });

  // ── Stagger stat cards ──
  document.querySelectorAll('.stat-card').forEach(function (card, i) {
    card.style.animationDelay = (0.05 + i * 0.07) + 's';
  });

});
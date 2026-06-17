// Main JavaScript for Vintage Newspaper

// Mobile Menu Toggle
document.addEventListener('DOMContentLoaded', function() {
    const hamburger = document.querySelector('.hamburger');
    const navLinks = document.querySelector('.nav-links');
    const backdrop = document.getElementById('nav-backdrop');
    const navCloseBtn = document.getElementById('nav-close-btn');

    function closeMenu() {
        navLinks.classList.remove('active');
        hamburger.classList.remove('active');
        if (backdrop) backdrop.classList.remove('active');
        document.querySelectorAll('.has-dropdown.open').forEach(function (el) {
            el.classList.remove('open');
        });
    }

    if (hamburger && navLinks) {
        hamburger.addEventListener('click', function(e) {
            e.stopPropagation();
            const opening = !navLinks.classList.contains('active');
            navLinks.classList.toggle('active', opening);
            hamburger.classList.toggle('active', opening);
            if (backdrop) backdrop.classList.toggle('active', opening);
        });

        if (navCloseBtn) {
            navCloseBtn.addEventListener('click', closeMenu);
        }
        if (backdrop) {
            backdrop.addEventListener('click', closeMenu);
        }

        // Close menu when a real nav link is clicked (not the Categories toggle)
        navLinks.querySelectorAll('a').forEach(function(link) {
            if (link.classList.contains('dropdown-toggle')) return;
            link.addEventListener('click', function() {
                closeMenu();
            });
        });
    }

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
    
    // Auto-hide alerts after 5 seconds
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        }, 5000);
    });
});

// Search functionality (live search)
const searchInput = document.querySelector('input[name="q"]');
if (searchInput) {
    let debounceTimer;
    searchInput.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        debounceTimer = setTimeout(() => {
            // Can implement AJAX search here
            if (window.DEBUG) console.log('Search:', e.target.value);
        }, 500);
    });
}

// Image lazy loading
if ('IntersectionObserver' in window) {
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.classList.add('loaded');
                observer.unobserve(img);
            }
        });
    });
    
    document.querySelectorAll('img[data-src]').forEach(img => {
        imageObserver.observe(img);
    });
}

// Form validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let isValid = true;
        const inputs = form.querySelectorAll('input[required], textarea[required]');
        
        inputs.forEach(input => {
            if (!input.value.trim()) {
                isValid = false;
                input.style.borderColor = '#c33';
            } else {
                input.style.borderColor = '';
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Please fill in all required fields');
        }
    });
}

// Initialize
validateForm('loginForm');
validateForm('registerForm');

// ── Categories Dropdown (click for mobile, hover CSS handles desktop) ──
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.has-dropdown').forEach(function (item) {
    var toggle = item.querySelector('.dropdown-toggle');
    if (!toggle) return;

    toggle.addEventListener('click', function (e) {
      // Only intercept on mobile (hamburger visible)
      if (window.innerWidth <= 768) {
        e.preventDefault();
        item.classList.add('open');
      } else {
        // Desktop: prevent navigation on the "#" href
        e.preventDefault();
      }
    });
  });

  // Back / close buttons inside the category flyout
  document.querySelectorAll('#cat-back-btn, #cat-close-btn').forEach(function (btn) {
    btn.addEventListener('click', function (e) {
      e.preventDefault();
      e.stopPropagation();
      document.querySelectorAll('.has-dropdown.open').forEach(function (el) {
        el.classList.remove('open');
      });
    });
  });

  // Close dropdown on outside click (desktop only — mobile uses back/close buttons)
  document.addEventListener('click', function (e) {
    if (window.innerWidth > 768 && !e.target.closest('.has-dropdown')) {
      document.querySelectorAll('.has-dropdown.open').forEach(function (el) {
        el.classList.remove('open');
      });
    }
  });
});

// Init Lucide icons
if (typeof lucide !== "undefined") lucide.createIcons();
document.addEventListener("DOMContentLoaded", function() {
  if (typeof lucide !== "undefined") lucide.createIcons();
});
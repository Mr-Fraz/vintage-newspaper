/**
 * Dark Mode Toggle — Vintage Newspaper
 * Persists preference in localStorage
 */
(function () {
  const STORAGE_KEY = 'vn_dark_mode';
  const DARK_CLASS  = 'dark-mode';

  // Apply saved preference BEFORE paint (no flash)
  function applyPreference() {
    if (localStorage.getItem(STORAGE_KEY) === 'dark') {
      document.body.classList.add(DARK_CLASS);
    }
  }

  function updateButton(isDark) {
    const btn   = document.getElementById('dark-toggle-btn');
    const icon  = document.getElementById('toggle-icon');
    const label = document.getElementById('toggle-label');
    if (!btn) return;
    if (isDark) {
      icon.textContent  = '☀️';
      label.textContent = 'Light';
      btn.setAttribute('aria-label', 'Switch to light mode');
      btn.setAttribute('title', 'Switch to light mode');
    } else {
      icon.textContent  = '🌙';
      label.textContent = 'Dark';
      btn.setAttribute('aria-label', 'Switch to dark mode');
      btn.setAttribute('title', 'Switch to dark mode');
    }
  }

  function toggle() {
    const isDark = document.body.classList.toggle(DARK_CLASS);
    localStorage.setItem(STORAGE_KEY, isDark ? 'dark' : 'light');
    updateButton(isDark);
  }

  // Apply before DOM ready to prevent flash
  applyPreference();

  document.addEventListener('DOMContentLoaded', function () {
    const btn = document.getElementById('dark-toggle-btn');
    if (btn) {
      btn.addEventListener('click', toggle);
      updateButton(document.body.classList.contains(DARK_CLASS));
    }
  });
})();
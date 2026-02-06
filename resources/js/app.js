import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * GLOBAL theme helpers (used by all Blade layouts/partials)
 */
window.setTheme = (theme) => {
  try { localStorage.setItem('theme', theme); } catch (e) { }
  document.documentElement.setAttribute('data-theme', theme);

  // Sync all theme controllers (checkboxes)
  document.querySelectorAll('.theme-controller').forEach(el => {
    el.checked = (theme === 'dark');
  });
};

// Initialize theme on load (backup if inline script missed it)
(() => {
  let saved = 'light';
  try {
    saved = localStorage.getItem('theme') ||
      (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light');
  } catch (e) { }

  // Only set if not already set by inline script to avoid double-paint
  if (!document.documentElement.hasAttribute('data-theme')) {
    window.setTheme(saved);
  }

  // Sync checkboxes on DOM ready
  document.addEventListener('DOMContentLoaded', () => {
    const currentTheme = document.documentElement.getAttribute('data-theme');
    document.querySelectorAll('.theme-controller').forEach(el => {
      el.checked = (currentTheme === 'dark');

      // Add listener
      el.addEventListener('change', (e) => {
        window.setTheme(e.target.checked ? 'dark' : 'light');
      });
    });
  });
})();

Alpine.start();

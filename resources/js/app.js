import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * GLOBAL theme helpers (used by all Blade layouts/partials)
 * - window.setTheme(theme)     => applies daisyUI theme + persists
 * - window.toggleTheme(dark)   => convenience: true => "dim", false => "nord"
 */
window.setTheme = (theme) => {
  try { localStorage.setItem('theme', theme); } catch (e) {}
  // Apply daisyUI theme
  document.documentElement.setAttribute('data-theme', theme);
  // If you also use Tailwind `dark:` variants anywhere, keep this line:
  // document.documentElement.classList.toggle('dark', theme === 'dim');

  // Optional: notify any listeners that theme changed (useful for live labels)
  window.dispatchEvent(new CustomEvent('theme:changed', { detail: { theme } }));
};

window.toggleTheme = (dark) => window.setTheme(dark ? 'dim' : 'nord');

// Apply saved theme immediately on load
(() => {
  let saved = 'nord';
  try { saved = localStorage.getItem('theme') || saved; } catch (e) {}
  window.setTheme(saved);
})();

Alpine.start();

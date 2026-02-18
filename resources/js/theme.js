(function () {
  const key = 'theme';
  const root = document.documentElement;

  function apply(theme) {
    if (theme === 'dark') root.classList.add('dark');
    else root.classList.remove('dark');
  }

  const saved = localStorage.getItem(key);
  if (saved === 'dark' || saved === 'light') {
    apply(saved);
  } else {
    const prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
    apply(prefersDark ? 'dark' : 'light');
  }

  window.__toggleTheme = function () {
    const isDark = root.classList.contains('dark');
    const next = isDark ? 'light' : 'dark';
    localStorage.setItem(key, next);
    apply(next);
    window.dispatchEvent(new CustomEvent('theme-changed', { detail: { theme: next } }));
  };
})();

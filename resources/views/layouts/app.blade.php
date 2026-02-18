<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>{{ $title ?? 'Pembantu.web.id' }}</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            gray: {
              950: '#030712',
            }
          }
        }
      }
    }
  </script>
  <script>
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
  </script>
  @livewireStyles
</head>
<body class="min-h-full bg-white text-gray-900 dark:bg-gray-950 dark:text-gray-100 transition-colors duration-200">
  <header class="sticky top-0 z-30 border-b border-gray-200 bg-white/80 backdrop-blur dark:border-gray-800 dark:bg-gray-950/80">
    <div class="mx-auto max-w-6xl px-4 py-3 flex items-center justify-between gap-3">
      <a href="{{ url('/') }}" class="flex items-center gap-2">
        <div class="h-9 w-9 rounded-xl border border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-gray-900 flex items-center justify-center">
          <x-icon.logo class="h-5 w-5" />
        </div>
        <div>
          <div class="font-semibold leading-tight">Pembantu.web.id</div>
          <div class="text-xs text-gray-600 dark:text-gray-400">Portal jasa tenaga kerja</div>
        </div>
      </a>

      <nav class="flex items-center gap-2">
        <a href="{{ route('search') }}"
           class="hidden sm:inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm
                  border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900">
          <x-icon.search class="h-4 w-4" />
          <span>Cari</span>
        </a>

        <button type="button" onclick="window.__toggleTheme()"
          class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm
                 border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900"
          aria-label="Toggle dark mode">
          <span class="hidden dark:inline">
            <x-icon.sun class="h-4 w-4" />
          </span>
          <span class="inline dark:hidden">
            <x-icon.moon class="h-4 w-4" />
          </span>
          <span class="hidden sm:inline">Mode</span>
        </button>
      </nav>
    </div>
  </header>

  <main class="mx-auto max-w-6xl px-4 py-6">
    {{ $slot }}
  </main>

  <footer class="border-t border-gray-200 dark:border-gray-800">
    <div class="mx-auto max-w-6xl px-4 py-6 text-sm text-gray-600 dark:text-gray-400">
      © {{ date('Y') }} Pembantu.web.id
    </div>
  </footer>

  @livewireScripts
</body>
</html>

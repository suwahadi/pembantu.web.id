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
           class="hidden sm:inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium
                  border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900 transition-colors">
          <x-icon.search class="h-4 w-4" />
          <span>Cari</span>
        </a>

        @guest
          <a href="{{ route('login') }}"
             class="inline-flex items-center gap-2 rounded-xl border px-4 py-2 text-sm font-medium
                    border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900 transition-colors">
            <span>Masuk</span>
          </a>
          <a href="{{ route('register') }}"
             class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-medium
                    text-white hover:bg-blue-700 transition-colors">
            <span>Daftar</span>
          </a>
        @else
          <div class="relative group">
            <button type="button" 
                    class="inline-flex items-center gap-2 rounded-xl border px-3 py-2 text-sm font-medium
                           border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900 transition-colors">
              <div class="h-5 w-5 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center text-blue-600 dark:text-blue-400 text-[10px] uppercase font-bold">
                {{ substr(auth()->user()->name, 0, 2) }}
              </div>
              <span class="hidden md:inline">{{ auth()->user()->name }}</span>
              <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>
            
            <div class="absolute right-0 mt-2 w-48 scale-95 opacity-0 invisible group-hover:scale-100 group-hover:opacity-100 group-hover:visible transition-all duration-200 origin-top-right">
              <div class="rounded-2xl border border-gray-100 bg-white p-2 shadow-xl dark:border-gray-800 dark:bg-gray-900">
                @if(auth()->user()->hasRole('admin'))
                  <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800">
                    Dashboard Admin
                  </a>
                @elseif(auth()->user()->hasRole('agency'))
                  <a href="{{ route('agency.dashboard') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
                    Dashboard Agensi
                  </a>
                @endif
                <a href="{{ route('profile') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
                  Profil Saya
                </a>
                <a href="{{ route('orders.list') }}" class="flex items-center gap-2 rounded-xl px-3 py-2 text-sm text-gray-600 hover:bg-gray-50 dark:text-gray-400 dark:hover:bg-gray-800 transition-colors">
                  Pesanan Saya
                </a>
                <div class="my-1 border-t border-gray-100 dark:border-gray-800"></div>
                <form method="POST" action="{{ route('logout') }}">
                  @csrf
                  <button type="submit" class="flex w-full items-center gap-2 rounded-xl px-3 py-2 text-sm text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20">
                    Keluar
                  </button>
                </form>
              </div>
            </div>
          </div>
        @endguest

        <button type="button" onclick="window.__toggleTheme()"
          class="inline-flex items-center justify-center h-10 w-10 rounded-xl border
                 border-gray-200 hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-gray-900 transition-colors"
          aria-label="Toggle dark mode">
          <span class="hidden dark:inline">
            <x-icon.sun class="h-4 w-4" />
          </span>
          <span class="inline dark:hidden">
            <x-icon.moon class="h-4 w-4" />
          </span>
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

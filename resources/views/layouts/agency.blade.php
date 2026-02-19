<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Agency - Pembantu.web.id' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tailwind CSS CDN as fallback -->
    <script src="https://cdn.tailwindcss.com"></script>
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 dark:bg-gray-900 dark:text-gray-100">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[260px_1fr]">
        <aside class="bg-white border-r dark:bg-gray-800 dark:border-gray-700">
            <div class="p-4 border-b dark:border-gray-700 flex items-center justify-between">
                <div>
                    <div class="font-semibold dark:text-white">Pembantu.web.id</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">Panel Agency</div>
                </div>
                <button onclick="document.documentElement.classList.toggle('dark')" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition" title="Toggle Dark Mode">
                    <svg class="w-5 h-5 hidden dark:block" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <svg class="w-5 h-5 block dark:hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/></svg>
                </button>
            </div>

            @php
                $nav = [
                    ['label' => 'Dashboard', 'route' => 'agency.dashboard'],
                    ['label' => 'Kontrak', 'route' => 'agency.contracts'],
                    ['label' => 'Worker', 'route' => 'agency.workers.index'],
                    ['label' => 'Order', 'route' => 'agency.orders.index'],
                    ['label' => 'Profil', 'route' => 'profile'],
                    ['label' => 'Rekening Bank', 'route' => 'agency.bank-accounts'],
                ];
            @endphp

            <nav class="p-3 space-y-1 text-sm">
                @foreach($nav as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                        class="block rounded-lg px-3 py-2 border {{ $active ? 'bg-gray-900 text-white border-gray-900 dark:bg-white dark:text-gray-900 dark:border-white' : 'bg-white border-transparent hover:bg-gray-50 dark:bg-transparent dark:hover:bg-gray-700/50 dark:text-gray-300' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t text-xs text-gray-600 dark:border-gray-700 dark:text-gray-400">
                Agency: <span class="font-medium dark:text-white">{{ auth()->user()->agency->company_name ?? '-' }}</span>
            </div>
        </aside>

        <main class="p-4 lg:p-6">
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-xl p-3 text-sm dark:bg-green-900/20 dark:border-green-800 dark:text-green-200">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-xl p-3 text-sm dark:bg-red-900/20 dark:border-red-800 dark:text-red-200">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>

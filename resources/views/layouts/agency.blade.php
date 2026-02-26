<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Agency - Pembantu.web.id' }}</title>

    <script>
        (function() {
            var s = localStorage.getItem('theme');
            var d = (!s && window.matchMedia('(prefers-color-scheme: dark)').matches) || s === 'dark';
            if (d) document.documentElement.classList.add('dark');
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{ 
        sidebarExpanded: true,
        mobileMenuOpen: false,
        darkMode: document.documentElement.classList.contains('dark'),
        
        init() {
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1280) {
                    this.mobileMenuOpen = false;
                }
            });
            window.addEventListener('theme-changed', (e) => {
                this.darkMode = e.detail.theme === 'dark';
            });
        },
        
        toggleSidebar() {
            this.sidebarExpanded = !this.sidebarExpanded;
        },
        
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        },

        toggleTheme() {
            this.darkMode = !this.darkMode;
            var next = this.darkMode ? 'dark' : 'light';
            localStorage.setItem('theme', next);
            if (this.darkMode) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        }
    }">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        @include('layouts.backdrop')
        @include('layouts.agency-sidebar')

        <div class="transition-all duration-300 ease-in-out"
             :class="{ 
                 'xl:ml-[290px]': sidebarExpanded, 
                 'xl:ml-[90px]': !sidebarExpanded,
                 'ml-0': true 
             }">
            @include('layouts.agency-header')
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 xl:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg p-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg p-4">
                        {{ session('error') }}
                    </div>
                @endif

                @hasSection('content')
                    @yield('content')
                @elseif (isset($slot))
                    {{ $slot }}
                @endif
            </div>
        </div>
    </div>

    <div x-show="mobileMenuOpen" 
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="mobileMenuOpen = false"
         class="fixed inset-0 z-40 bg-gray-900 bg-opacity-50 xl:hidden"></div>

    @livewireScripts
</body>
</html>

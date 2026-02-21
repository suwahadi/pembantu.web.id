<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Admin - Pembantu.web.id' }}</title>

    <!-- Apply dark mode immediately to prevent flash -->
    <script>
        (function() {
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initDarkMode);
            } else {
                initDarkMode();
            }

            function initDarkMode() {
                const savedTheme = localStorage.getItem('theme');
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                const theme = savedTheme || (prefersDark ? 'dark' : 'light');

                if (theme === 'dark') {
                    if (document.documentElement) {
                        document.documentElement.classList.add('dark');
                    }
                    if (document.body) {
                        document.body.classList.add('dark');
                    }
                } else {
                    if (document.documentElement) {
                        document.documentElement.classList.remove('dark');
                    }
                    if (document.body) {
                        document.body.classList.remove('dark');
                    }
                }
            }
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body x-data="{ 
        sidebarExpanded: true,
        mobileMenuOpen: false,
        
        init() {
            // Handle window resize
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1280) {
                    this.mobileMenuOpen = false;
                }
            });
        },
        
        toggleSidebar() {
            this.sidebarExpanded = !this.sidebarExpanded;
        },
        
        toggleMobileMenu() {
            this.mobileMenuOpen = !this.mobileMenuOpen;
        }
    }">
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
        @include('layouts.backdrop')
        @include('layouts.admin-sidebar')

        <!-- Main Content -->
        <div class="transition-all duration-300 ease-in-out"
             :class="{ 
                 'xl:ml-[290px]': sidebarExpanded, 
                 'xl:ml-[90px]': !sidebarExpanded,
                 'ml-0': true 
             }">
            <!-- app header start -->
            @include('layouts.admin-header')
            <!-- app header end -->
            <div class="p-4 mx-auto max-w-7xl md:p-6">
                @hasSection('content')
                    @yield('content')
                @elseif (isset($slot))
                    {{ $slot }}
                @endif
            </div>
        </div>
    </div>

    <!-- Mobile Menu Backdrop -->
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

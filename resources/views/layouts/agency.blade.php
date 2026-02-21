<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'Agency - Pembantu.web.id' }}</title>

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
                        document.body.classList.add('dark', 'bg-gray-900');
                    }
                } else {
                    if (document.documentElement) {
                        document.documentElement.classList.remove('dark');
                    }
                    if (document.body) {
                        document.body.classList.remove('dark', 'bg-gray-900');
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
        @include('layouts.agency-sidebar')

        <!-- Main Content -->
        <div class="transition-all duration-300 ease-in-out"
             :class="{ 
                 'xl:ml-[290px]': sidebarExpanded, 
                 'xl:ml-[90px]': !sidebarExpanded,
                 'ml-0': true 
             }">
            <!-- app header start -->
            @include('layouts.agency-header')
            <!-- app header end -->
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6 xl:px-8">
                @if (session('success'))
                    <div class="mb-4 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 text-success-700 dark:text-success-300 rounded-lg p-4">
                        {{ session('success') }}
                    </div>
                @endif
                @if (session('error'))
                    <div class="mb-4 bg-error-50 dark:bg-error-900/20 border border-error-200 dark:border-error-800 text-error-700 dark:text-error-300 rounded-lg p-4">
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

    @livewireScripts
</body>

</html>

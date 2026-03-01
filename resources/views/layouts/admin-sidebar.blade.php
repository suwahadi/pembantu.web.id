<aside id="sidebar"
    class="fixed top-0 left-0 z-50 flex flex-col h-screen bg-white border-r border-gray-200 transition-all duration-300 ease-in-out dark:bg-gray-800 dark:border-gray-700"
    :class="{
        'w-[290px]': sidebarExpanded,
        'w-[90px]': !sidebarExpanded,
        '-translate-x-full': !mobileMenuOpen,
        'translate-x-0': mobileMenuOpen,
        'xl:translate-x-0': true
    }">

    <div class="flex items-center justify-between h-16 px-6 border-b border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-8 h-8 rounded-lg bg-primary-600">
                <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
            </div>
            <div x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">
                <div class="text-lg font-semibold text-gray-900 dark:text-white">Pembantu</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Admin Panel</div>
            </div>
        </div>
        
        <button @click="toggleSidebar()" 
                class="hidden xl:flex items-center justify-center w-8 h-8 text-gray-500 rounded-lg hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-gray-700 transition-colors">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
            </svg>
        </button>
    </div>

    <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
        <p x-show="sidebarExpanded" class="px-4 mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Menu Utama</p>

        <a href="{{ route('admin.dashboard') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.dashboard') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Dashboard</span>
        </a>

        <a href="{{ route('admin.users.index') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.users.*') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Users</span>
        </a>

        <a href="{{ route('admin.orders.index') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.orders.*') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Orders</span>
        </a>

        <a href="{{ route('admin.disputes') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.disputes') || request()->routeIs('admin.disputes.show') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16h6M3 5a2 2 0 012-2h14a2 2 0 012 2v12a2 2 0 01-2 2H7l-4 3V5z" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Dispute</span>
        </a>

        <a href="{{ route('admin.agencies.index') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.agencies.*') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Agencies</span>
        </a>

        <p x-show="sidebarExpanded" class="px-4 mt-6 mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Keuangan</p>

         <a href="{{ route('admin.payment.index') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.payment.*') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Payment</span>
        </a>

        <a href="{{ route('admin.escrow.index') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.escrow.*') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-10c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Escrow</span>
        </a>

        <a href="{{ route('admin.payout-queue') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.payout-queue') || request()->routeIs('admin.payouts') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Payout Queue</span>
        </a>

        <a href="{{ route('admin.refunds') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.refunds') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Refunds</span>
        </a>

        <a href="{{ route('admin.bank-accounts') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.bank-accounts') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Bank Accounts</span>
        </a>

        <p x-show="sidebarExpanded" class="px-4 mt-6 mb-2 text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">Pengaturan</p>

        <a href="{{ route('admin.profile') }}" 
           class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.profile') ? 'bg-primary-50 text-primary-600 dark:bg-primary-600/10 dark:text-primary-400' : 'text-gray-700 hover:bg-gray-100 dark:text-gray-300 dark:hover:bg-gray-700' }}">
            <svg class="w-5 h-5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            <span x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100">Profile</span>
        </a>
    </nav>

    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-3">
            <div class="flex items-center justify-center w-10 h-10 rounded-full bg-primary-100 dark:bg-primary-600/20">
                <span class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ substr(auth()->user()->name, 0, 2) }}</span>
            </div>
            <div x-show="sidebarExpanded" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="flex-1 min-w-0">
                <div class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ auth()->user()->name }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400">Admin</div>
            </div>
        </div>
    </div>
</aside>

<nav class="bg-white shadow">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="/" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold">P</span>
                    </div>
                    <span class="text-xl font-bold text-gray-900">Pembantu</span>
                </a>
            </div>

            <!-- Navigation Links (Desktop) -->
            <div class="hidden md:flex items-center space-x-8">
                <a href="/" class="text-gray-600 hover:text-gray-900 transition">Beranda</a>
                <a href="/search" class="text-gray-600 hover:text-gray-900 transition">Cari Jasa</a>
                <a href="/tentang" class="text-gray-600 hover:text-gray-900 transition">Tentang</a>
                <a href="/kontak" class="text-gray-600 hover:text-gray-900 transition">Kontak</a>
            </div>

            <!-- Auth Links / User Menu -->
            <div class="flex items-center space-x-4">
                @guest
                    <a href="/login" class="text-gray-600 hover:text-gray-900 transition">
                        @include('svgs.icon-user', ['class' => 'w-5 h-5'])
                    </a>
                    <a href="/login" class="px-4 py-2 text-gray-700 hover:bg-gray-100 rounded-lg transition">
                        Masuk
                    </a>
                    <a href="/register" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                        Daftar
                    </a>
                @else
                    <div class="flex items-center space-x-4">
                        <button wire:click="search" class="text-gray-600 hover:text-gray-900">
                            @include('svgs.icon-search', ['class' => 'w-5 h-5'])
                        </button>
                        <div class="relative group">
                            <button class="flex items-center space-x-2 text-gray-700">
                                @include('svgs.icon-user', ['class' => 'w-5 h-5'])
                                <span>{{ auth()->user()->name }}</span>
                            </button>
                            <div class="hidden group-hover:block absolute right-0 mt-2 w-48 bg-white shadow rounded-lg">
                                <a href="/dashboard" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Dashboard</a>
                                <a href="/orders" class="block px-4 py-2 text-gray-700 hover:bg-gray-100">Pesanan</a>
                                <form method="POST" action="/logout" class="block">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-4 py-2 text-gray-700 hover:bg-gray-100">Keluar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endguest
            </div>

            <!-- Mobile Menu Button -->
            <button class="md:hidden">
                @include('svgs.icon-menu', ['class' => 'w-6 h-6'])
            </button>
        </div>
    </div>
</nav>

<div class="w-full">
    <!-- Hero Section -->
    <section class="bg-gradient-to-r from-blue-600 to-blue-800 text-white py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-4xl md:text-5xl font-bold mb-4">Temukan Tenaga Kerja Profesional</h1>
                <p class="text-lg md:text-xl text-blue-100 mb-8">Platform terpercaya untuk menemukan jasa layanan profesional di Indonesia</p>
                
                <!-- Search Bar -->
                <div class="flex flex-col md:flex-row gap-3 max-w-2xl mx-auto">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            wire:model.live.debounce-300ms="search_query"
                            wire:keydown.enter="search"
                            placeholder="Cari jasa atau lokasi..." 
                            class="w-full px-6 py-3 rounded-lg text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-300"
                        >
                        <button 
                            wire:click="search"
                            class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600"
                        >
                            <x-icon.search class="w-5 h-5" />
                        </button>
                    </div>
                    <button 
                        wire:click="search"
                        class="px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition"
                    >
                        Cari
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-16 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Kategori Layanan</h2>
            <p class="text-gray-600 mb-8">Jelajahi berbagai kategori layanan profesional yang tersedia</p>
            
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @foreach($categories as $category)
                    <a href="/search?category={{ $category->slug }}" class="group">
                        <div class="bg-gradient-to-br {{ $loop->index % 3 == 0 ? 'from-blue-600 to-indigo-700' : ($loop->index % 3 == 1 ? 'from-purple-600 to-pink-700' : 'from-emerald-600 to-teal-700') }} p-6 rounded-2xl text-white text-center hover:shadow-xl transition transform hover:-translate-y-1">
                            <div class="mb-3 flex justify-center text-4xl">
                                @if($category->slug === 'babysitter')
                                    <x-icon.baby class="h-10 w-10" />
                                @elseif($category->slug === 'sopir')
                                    <x-icon.car class="h-10 w-10" />
                                @elseif($category->slug === 'perawat-lansia')
                                    <x-icon.heart class="h-10 w-10" />
                                @elseif($category->slug === 'art-prt')
                                    <x-icon.home class="h-10 w-10" />
                                @elseif($category->slug === 'tukang-kebun')
                                    <x-icon.plant class="h-10 w-10" />
                                @else
                                    <x-icon.logo class="h-10 w-10" />
                                @endif
                            </div>
                            <h3 class="font-bold text-sm tracking-wide">{{ $category->name }}</h3>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Workers Section -->
    <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 class="text-3xl font-bold text-gray-900 mb-2">Pekerja Pilihan</h2>
            <p class="text-gray-600 mb-8">Pekerja profesional terbaik yang telah terverifikasi</p>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($featured_workers as $worker)
                    <x-worker-card :worker="$worker" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-16 bg-blue-600 text-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl font-bold mb-4">Mulai Cari Jasa Sekarang</h2>
            <p class="text-blue-100 text-lg mb-8">Ribuan pekerja profesional siap membantu Anda</p>
            <a href="/search" class="inline-block px-8 py-3 bg-white text-blue-600 font-semibold rounded-lg hover:bg-gray-100 transition">
                Jelajahi Semua Jasa
            </a>
        </div>
    </section>
</div>

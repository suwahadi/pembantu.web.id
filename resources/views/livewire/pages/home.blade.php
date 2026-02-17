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
                            @include('svgs.icon-search', ['class' => 'w-5 h-5'])
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
                        <div class="bg-gradient-to-br {{ $loop->index % 3 == 0 ? 'from-green-400 to-blue-500' : ($loop->index % 3 == 1 ? 'from-purple-400 to-pink-500' : 'from-yellow-400 to-orange-500') }} p-6 rounded-lg text-white text-center hover:shadow-lg transition transform hover:scale-105">
                            <div class="mb-2 flex justify-center">
                                @switch($category->id % 5)
                                    @case(0)
                                        @include('svgs.icon-home', ['class' => 'w-8 h-8'])
                                    @break
                                    @case(1)
                                        @include('svgs.icon-user', ['class' => 'w-8 h-8'])
                                    @break
                                    @case(2)
                                        @include('svgs.icon-star', ['class' => 'w-8 h-8'])
                                    @break
                                    @case(3)
                                        @include('svgs.icon-location', ['class' => 'w-8 h-8'])
                                    @break
                                    @default
                                        @include('svgs.icon-search', ['class' => 'w-8 h-8'])
                                @endswitch
                            </div>
                            <h3 class="font-semibold text-sm">{{ $category->name }}</h3>
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
                    <a href="/worker/{{ $worker->id }}" class="group">
                        <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                            <!-- Worker Image -->
                            <div class="bg-gradient-to-br from-blue-400 to-blue-600 h-48 flex items-center justify-center text-white text-4xl">
                                {{ substr($worker->name, 0, 1) }}
                            </div>
                            
                            <!-- Worker Info -->
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 group-hover:text-blue-600 transition">{{ $worker->name }}</h3>
                                <p class="text-sm text-gray-600 mb-2">{{ $worker->category->name }}</p>
                                
                                <!-- Rating -->
                                <div class="flex items-center space-x-1 mb-3">
                                    <span class="text-yellow-500">
                                        @include('svgs.icon-star', ['class' => 'w-4 h-4'])
                                    </span>
                                    <span class="text-sm font-semibold text-gray-900">4.8</span>
                                    <span class="text-xs text-gray-500">({{ rand(20, 200) }} ulasan)</span>
                                </div>
                                
                                <!-- Location -->
                                <div class="flex items-center space-x-1 text-xs text-gray-600 mb-3">
                                    @include('svgs.icon-location', ['class' => 'w-3 h-3'])
                                    <span>{{ $worker->location->name }}</span>
                                </div>
                                
                                <!-- Price -->
                                <p class="text-lg font-bold text-blue-600">
                                    Rp {{ number_format($worker->pricings->first()?->price_idr ?? 150000, 0, ',', '.') }}
                                </p>
                            </div>
                        </div>
                    </a>
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

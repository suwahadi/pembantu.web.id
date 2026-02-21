<div class="w-full">
    <!-- Hero Section -->
    <section class="relative bg-gradient-to-r from-blue-600 to-blue-800 dark:from-blue-900 dark:to-indigo-950 text-white py-24 overflow-hidden">
        <!-- Abstract Decoration -->
        <div class="absolute top-0 right-0 -translate-y-1/2 translate-x-1/4 w-96 h-96 bg-blue-400/20 dark:bg-blue-500/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-0 left-0 translate-y-1/2 -translate-x-1/4 w-64 h-64 bg-indigo-400/20 dark:bg-indigo-500/10 rounded-full blur-3xl"></div>

        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto">
                <h1 class="text-4xl md:text-6xl font-extrabold mb-6 tracking-tight leading-tight">
                    Temukan Tenaga Kerja <span class="text-blue-200">Profesional</span>
                </h1>
                <p class="text-lg md:text-xl text-blue-100/90 mb-10 font-medium">
                    Platform terpercaya untuk menemukan solusi jasa terbaik dari ribuan pekerja terverifikasi di seluruh Indonesia.
                </p>
                
                <!-- Search Bar -->
                <div class="flex flex-col md:flex-row gap-3 max-w-2xl mx-auto group">
                    <div class="flex-1 relative">
                        <input 
                            type="text" 
                            wire:model.live.debounce-300ms="search_query"
                            wire:keydown.enter="search"
                            placeholder="Cari jasa (Contoh: Babysitter, Sopir...)" 
                            class="w-full px-6 py-4 rounded-2xl text-gray-900 bg-white/95 backdrop-blur-sm border-2 border-transparent focus:outline-none focus:ring-4 focus:ring-blue-500/30 focus:border-blue-400 transition-all shadow-xl"
                        >
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400">
                            <x-icon.search class="w-5 h-5" />
                        </div>
                    </div>
                    <button 
                        wire:click="search"
                        class="px-10 py-4 bg-white dark:bg-blue-500 text-blue-700 dark:text-white font-bold rounded-2xl hover:bg-gray-100 dark:hover:bg-blue-600 transition-all shadow-xl active:scale-[0.98]"
                    >
                        Cari Sekarang
                    </button>
                </div>

                <div class="mt-8 flex flex-wrap justify-center gap-4 text-sm font-medium text-blue-100/80">
                    <span>Populer:</span>
                    <a href="/search?category=art-prt" class="hover:text-white underline decoration-blue-400/50">ART</a>
                    <a href="/search?category=babysitter" class="hover:text-white underline decoration-blue-400/50">Babysitter</a>
                    <a href="/search?category=sopir" class="hover:text-white underline decoration-blue-400/50">Sopir</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Categories Section -->
    <section class="py-20 bg-white dark:bg-gray-950 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 gap-4">
                <div>
                    <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-3">Kategori Layanan</h2>
                    <p class="text-gray-500 dark:text-gray-400 font-medium max-w-xl">Jelajahi berbagai spesialisasi tenaga kerja profesional sesuai kebutuhan Anda.</p>
                </div>
                <a href="/search" class="text-blue-600 dark:text-blue-400 font-bold hover:underline flex items-center gap-2">
                    Lihat Semua <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
                @foreach($categories as $category)
                    <a href="/search?category={{ $category->slug }}" class="group relative">
                        <div class="h-full bg-gradient-to-br {{ $loop->index % 3 == 0 ? 'from-blue-600 to-indigo-700' : ($loop->index % 3 == 1 ? 'from-purple-600 to-pink-700' : 'from-emerald-600 to-teal-700') }} p-6 rounded-[2rem] text-white text-center shadow-lg group-hover:shadow-2xl transition-all duration-300 transform group-hover:-translate-y-2 border-4 border-transparent dark:group-hover:border-white/20">
                            <div class="mb-4 flex justify-center">
                                @if($category->slug === 'babysitter')
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl group-hover:bg-white/30 transition-all"></div>
                                        <svg class="relative h-12 w-12 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                        </svg>
                                    </div>
                                @elseif($category->slug === 'sopir')
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl group-hover:bg-white/30 transition-all"></div>
                                        <svg class="relative h-12 w-12 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M18.92 6.01C18.72 5.42 18.16 5 17.5 5h-11c-.66 0-1.21.42-1.42 1.01L3 12v8c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-1h12v1c0 .55.45 1 1 1h1c.55 0 1-.45 1-1v-8l-2.08-5.99zM6.5 16c-.83 0-1.5-.67-1.5-1.5S5.67 13 6.5 13s1.5.67 1.5 1.5S7.33 16 6.5 16zm11 0c-.83 0-1.5-.67-1.5-1.5s.67-1.5 1.5-1.5 1.5.67 1.5 1.5-.67 1.5-1.5 1.5zM5 11l1.5-4.5h11L19 11H5z"/>
                                        </svg>
                                    </div>
                                @elseif($category->slug === 'perawat-lansia')
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl group-hover:bg-white/30 transition-all"></div>
                                        <svg class="relative h-12 w-12 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                        </svg>
                                    </div>
                                @elseif($category->slug === 'art-prt')
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl group-hover:bg-white/30 transition-all"></div>
                                        <svg class="relative h-12 w-12 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M10 20v-6h4v6h5v-8h3L12 3 2 12h3v8z"/>
                                        </svg>
                                    </div>
                                @elseif($category->slug === 'tukang-kebun')
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl group-hover:bg-white/30 transition-all"></div>
                                        <svg class="relative h-12 w-12 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 3c1.66 0 3 1.34 3 3s-1.34 3-3 3-3-1.34-3-3 1.34-3 3-3zm0 14.2c-2.5 0-4.71-1.28-6-3.22.03-1.99 4-3.08 6-3.08 1.99 0 5.97 1.09 6 3.08-1.29 1.94-3.5 3.22-6 3.22z"/>
                                        </svg>
                                    </div>
                                @elseif(str_contains($category->slug, 'keamanan') || str_contains($category->slug, 'satpam'))
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl group-hover:bg-white/30 transition-all"></div>
                                        <svg class="relative h-12 w-12 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                                        </svg>
                                    </div>
                                @else
                                    <div class="relative">
                                        <div class="absolute inset-0 bg-white/20 rounded-full blur-xl group-hover:bg-white/30 transition-all"></div>
                                        <svg class="relative h-12 w-12 transform group-hover:scale-110 transition-transform" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <h3 class="font-extrabold text-sm tracking-wide mb-1">{{ $category->name }}</h3>
                            <div class="w-8 h-1 bg-white/30 mx-auto rounded-full group-hover:w-12 transition-all"></div>
                        </div>
                    </a>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Featured Workers Section -->
    <section class="py-20 bg-gray-50 dark:bg-gray-900/50 transition-colors duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-extrabold text-gray-900 dark:text-white mb-4 italic">Pekerja Pilihan</h2>
                <div class="w-24 h-1.5 bg-blue-600 mx-auto rounded-full mb-6"></div>
                <p class="text-gray-500 dark:text-gray-400 font-medium max-w-2xl mx-auto text-lg">Hanya menampilkan tenaga kerja profesional dengan rating terbaik dan verifikasi dokumen lengkap.</p>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                @foreach($featured_workers as $worker)
                    <x-worker-card :worker="$worker" />
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-24 bg-blue-600 dark:bg-blue-800 text-white relative overflow-hidden">
        <div class="absolute inset-0 opacity-10">
            <svg class="h-full w-full" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                <defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/></pattern></defs>
                <rect width="100" height="100" fill="url(#grid)" />
            </svg>
        </div>
        
        <div class="relative max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-4xl md:text-5xl font-extrabold mb-6 tracking-tight">Siap Memulai Perubahan?</h2>
            <p class="text-blue-100 text-xl mb-10 font-medium leading-relaxed">
                Ribuan pekerja profesional terverifikasi siap membantu meringankan beban pekerjaan Anda hari ini.
            </p>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/search" class="px-10 py-4 bg-white text-blue-600 font-extrabold rounded-2xl hover:bg-gray-100 transition-all shadow-2xl shadow-blue-900/40 transform hover:-translate-y-1 active:scale-[0.98]">
                    Jelajahi Semua Jasa
                </a>
                @if(auth()->check())
                    <button 
                        wire:click="$dispatch('showAlreadyLoggedInAlert')" 
                        class="px-10 py-4 bg-blue-500 text-white font-extrabold rounded-2xl hover:bg-blue-400 border-2 border-blue-400/30 transition-all transform hover:-translate-y-1 active:scale-[0.98]"
                    >
                        Daftar Sebagai Pekerja
                    </button>
                @else
                    <a href="/register" class="px-10 py-4 bg-blue-500 text-white font-extrabold rounded-2xl hover:bg-blue-400 border-2 border-blue-400/30 transition-all transform hover:-translate-y-1 active:scale-[0.98]">
                        Daftar Sebagai Pekerja
                    </a>
                @endif
            </div>
        </div>
    </section>

    <!-- Alert for already logged in users -->
    <div x-data="{ show: false }" 
         x-show="show" 
         x-transition
         x-init="$listen('showAlreadyLoggedInAlert', () => { show = true })"
         class="fixed inset-x-0 top-4 z-50 mx-auto max-w-xl px-4">
        <div class="flex items-start justify-between rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-amber-800 dark:border-amber-800 dark:bg-amber-900/20 dark:text-amber-300">
            <p class="text-sm font-medium">Anda sudah login. Silakan logout terlebih dahulu jika ingin mendaftar sebagai pekerja baru.</p>
            <button type="button" class="ml-4 text-xs underline" @click="show = false">Tutup</button>
        </div>
    </div>
</div>

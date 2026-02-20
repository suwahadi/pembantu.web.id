<div>
    <div class="mb-6">
        <a href="{{ route('search') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400 hover:text-blue-500 dark:hover:text-blue-400">
            <x-icon.arrow-left class="h-4 w-4" />
            <span>Kembali ke Pencarian</span>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-8 lg:grid-cols-3">
        <!-- Left: Photo & Basic Info -->
        <div class="lg:col-span-1">
            <div class="sticky top-24">
                <div class="overflow-hidden rounded-3xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                    <div class="aspect-square bg-gray-100 dark:bg-gray-800">
                        @if($worker->photo_path)
                            <img src="{{ str_starts_with($worker->photo_path, 'http') ? $worker->photo_path : Storage::url($worker->photo_path) }}" 
                                 alt="{{ $worker->name }}" 
                                 class="h-full w-full object-cover">
                        @else
                            <div class="flex h-full w-full items-center justify-center text-gray-400">
                                <svg class="h-24 w-24" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-2">
                            <span class="inline-flex items-center rounded-lg bg-blue-50 dark:bg-blue-900/30 px-2.5 py-1 text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                                {{ $worker->category_name }}
                            </span>
                            <div class="flex items-center gap-1 text-sm font-bold text-orange-500">
                                <svg class="h-4 w-4 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                                <span>{{ number_format($worker->rating, 1) }}</span>
                            </div>
                        </div>
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $worker->name }}</h1>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mb-4 uppercase tracking-widest font-medium">ID: {{ $worker->public_id }}</p>
                        
                        <div class="space-y-3 pt-4 border-t border-gray-100 dark:border-gray-800">
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <x-icon.map-pin class="h-5 w-5 text-gray-400" />
                                <span>{{ $worker->primaryServiceArea?->location?->city ?? '-' }}</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <span>Agency: {{ $worker->agency?->company_name ?? '-' }}</span>
                            </div>
                        </div>

                        <div class="mt-8">
                            <a href="{{ route('checkout', $worker->id) }}" class="block w-full text-center rounded-2xl bg-blue-600 px-6 py-4 text-sm font-bold text-white shadow-xl shadow-blue-500/20 hover:bg-blue-700 transition-all active:scale-95">
                                Pesan Sekarang
                            </a>
                            <p class="mt-4 text-center text-xs text-gray-500 dark:text-gray-400">Gaji mulai: <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($worker->min_price ?? 0, 0, ',', '.') }}/hari</span></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Detailed Content -->
        <div class="lg:col-span-2 space-y-8">
            <!-- Bio Section -->
            <section class="rounded-3xl border border-gray-200 bg-white p-8 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Profil & Biografi</h2>
                <div class="prose prose-sm dark:prose-invert max-w-none text-gray-600 dark:text-gray-400 leading-relaxed">
                    {!! nl2br(e($worker->bio)) !!}
                </div>
            </section>

            <!-- Skills Section -->
            <section class="rounded-3xl border border-gray-200 bg-white p-8 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Keahlian & Pengalaman</h2>
                <div class="flex flex-wrap gap-2">
                    @if(isset($worker->skills) && $worker->skills->isNotEmpty())
                        @foreach($worker->skills as $skill)
                            <span class="inline-flex items-center rounded-xl bg-gray-50 dark:bg-gray-800 px-3 py-1.5 text-sm font-medium text-gray-700 dark:text-gray-300 border border-gray-100 dark:border-gray-700">
                                {{ $skill->name }}
                                @if($skill->pivot->is_primary)
                                    <span class="ml-1 text-xs text-blue-500 font-semibold">★</span>
                                @endif
                            </span>
                        @endforeach
                    @else
                        <span class="text-gray-500 dark:text-gray-400">Belum ada keahlian terdaftar</span>
                    @endif
                </div>
            </section>

            <!-- Pricing/Contract Info -->
            <section class="rounded-3xl border border-gray-200 bg-white p-8 dark:border-gray-800 dark:bg-gray-900 shadow-sm">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Informasi Tambahan</h2>
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-bold mb-1">Status Ketersediaan</p>
                        <p class="text-sm font-semibold text-green-600 dark:text-green-400 flex items-center gap-2">
                            <span class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></span>
                            Tersedia
                        </p>
                    </div>
                    <div class="p-4 rounded-2xl bg-gray-50 dark:bg-gray-800 border border-gray-100 dark:border-gray-700">
                        <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider font-bold mb-1">Skema Gaji Utama</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-white">
                            @if(isset($worker->defaultPricing) && $worker->defaultPricing)
                                {{ ucfirst($worker->defaultPricing->pricing_type) }}
                            @else
                                Harian
                            @endif
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>

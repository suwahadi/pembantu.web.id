<div class="min-h-screen bg-gray-50">
    <!-- Worker Header -->
    <section class="bg-white shadow">
        <div class="max-w-7xl mx-auto px-4 py-8">
            <a href="/search" class="text-blue-600 hover:text-blue-800 mb-4 inline-flex items-center gap-1">
                @include('svgs.icon-chevron-left', ['class' => 'w-5 h-5'])
                Kembali ke Pencarian
            </a>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Worker Avatar -->
                <div class="md:col-span-1">
                    <div class="w-full aspect-square rounded-lg bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center mb-4">
                        <span class="text-white text-8xl font-bold">{{ substr($worker->name, 0, 1) }}</span>
                    </div>
                    <button class="w-full px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition">
                        Pesan Jasa
                    </button>
                </div>

                <!-- Worker Info -->
                <div class="md:col-span-3">
                    <h1 class="text-4xl font-bold text-gray-900 mb-2">{{ $worker->name }}</h1>
                    <p class="text-xl text-gray-600 mb-4">{{ $worker->category->name }}</p>

                    <!-- Rating -->
                    <div class="flex items-center space-x-3 mb-6">
                        <span class="text-yellow-500 flex items-center">
                            <i class="hgi-stroke hgi-star w-5 h-5"></i>
                        </span>
                        <span class="text-2xl font-bold text-gray-900">{{ number_format($worker->rating, 1) }}</span>
                        <span class="text-gray-600">({{ $worker->total_reviews }} ulasan)</span>
                        <span class="text-gray-600">•</span>
                        <span class="text-gray-600">{{ $worker->total_completed_orders }} pesanan selesai</span>
                    </div>

                    <!-- Location -->
                    <div class="flex items-center space-x-2 text-gray-600 mb-4">
                        <i class="hgi-stroke hgi-map-pin w-5 h-5"></i>
                        <span>{{ $worker->location->name }}</span>
                    </div>

                    <!-- Verification Status -->
                    @if($worker->verification_status === 'verified')
                        <div class="inline-block px-4 py-2 bg-green-100 text-green-800 rounded-lg text-sm font-medium mb-6 flex items-center gap-1">
                            @include('svgs.icon-check', ['class' => 'w-4 h-4 text-green-800'])
                            Terverifikasi
                        </div>
                    @endif

                    <!-- Experience -->
                    <div class="bg-blue-50 rounded-lg p-4 mb-6">
                        <p class="text-gray-700"><span class="font-semibold">Pengalaman:</span> {{ $worker->experience_years }} tahun</p>
                        <p class="text-gray-700"><span class="font-semibold">Status:</span> 
                            @if($worker->is_available)
                                <span class="text-green-600">Tersedia</span>
                            @else
                                <span class="text-gray-600">Tidak Tersedia</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Worker Details -->
    <section class="max-w-7xl mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Main Content -->
            <div class="md:col-span-2">
                <!-- About -->
                <div class="bg-white rounded-lg shadow p-6 mb-8">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Tentang</h2>
                    <p class="text-gray-700 leading-relaxed">{{ $worker->bio }}</p>
                </div>

                <!-- Address -->
                @if($worker->address)
                    <div class="bg-white rounded-lg shadow p-6 mb-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-4">Alamat</h2>
                        <p class="text-gray-700">{{ $worker->address }}</p>
                    </div>
                @endif

                <!-- Reviews Section -->
                <div class="bg-white rounded-lg shadow p-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-6">Ulasan Klien</h2>
                    <div class="space-y-6">
                        <div class="border-b pb-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900">Klien Terverifikasi</p>
                                <div class="flex items-center space-x-1">
                                    @for($i = 0; $i < 5; $i++)
                                        <span class="text-yellow-500">
                                            <i class="hgi-stroke hgi-star w-4 h-4"></i>
                                        </span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-gray-700 mb-2">Layanan sangat profesional dan memuaskan!</p>
                            <p class="text-sm text-gray-500">2 minggu yang lalu</p>
                        </div>

                        <div class="border-b pb-6">
                            <div class="flex items-center justify-between mb-2">
                                <p class="font-semibold text-gray-900">Klien Terverifikasi</p>
                                <div class="flex items-center space-x-1">
                                    @for($i = 0; $i < 5; $i++)
                                        <span class="text-yellow-500">
                                            <i class="hgi-stroke hgi-star w-4 h-4"></i>
                                        </span>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-gray-700 mb-2">Sangat cepat dan rapi dalam bekerja!</p>
                            <p class="text-sm text-gray-500">1 bulan yang lalu</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pricing Sidebar -->
            <div class="md:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-20">
                    <h3 class="text-xl font-bold text-gray-900 mb-4">Paket Layanan</h3>

                    @forelse($worker->pricings as $pricing)
                        <div class="mb-6 pb-6 border-b last:border-b-0 last:pb-0">
                            <h4 class="font-semibold text-gray-900 mb-2">{{ $pricing->pricing_type }}</h4>
                            <p class="text-3xl font-bold text-blue-600 mb-2">
                                Rp {{ number_format($pricing->price_idr, 0, ',', '.') }}
                            </p>
                            @if($pricing->description)
                                <p class="text-sm text-gray-600 mb-2">{{ $pricing->description }}</p>
                            @endif
                            @if($pricing->min_duration || $pricing->max_duration)
                                <p class="text-xs text-gray-500 mb-4">
                                    @if($pricing->min_duration)
                                        Minimal: {{ $pricing->min_duration }} hari
                                    @endif
                                    @if($pricing->max_duration)
                                        | Maksimal: {{ $pricing->max_duration }} hari
                                    @endif
                                </p>
                            @endif
                            <button class="w-full px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                Pesan Paket Ini
                            </button>
                        </div>
                    @empty
                        <p class="text-gray-600">Belum ada paket layanan</p>
                    @endforelse

                    <!-- Contact -->
                    <div class="mt-6 pt-6 border-t">
                        <p class="text-sm text-gray-600 mb-3">Hubungi Langsung</p>
                        <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $worker->phone) }}" 
                           target="_blank"
                           class="flex items-center justify-center space-x-2 w-full px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-medium">
                            <span>WhatsApp</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

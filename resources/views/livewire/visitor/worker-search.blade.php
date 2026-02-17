<div class="space-y-4">
    <!-- Filter Section -->
    <x-card>
        <div class="grid grid-cols-1 md:grid-cols-5 gap-3">
            <x-form.input label="Cari..." wire:model.live="q" placeholder="Nama, skill, deskripsi..." />
            <x-form.select label="Kategori" wire:model.live="categoryId" :options="$categories" />
            <x-form.select label="Lokasi" wire:model.live="locationId" :options="$locations" />
            <x-form.select label="Skema" wire:model.live="scheme" :options="$schemes" />
            <x-form.select label="Urutkan" wire:model.live="sortBy"
                :options="['rating' => 'Rating', 'price_low' => 'Harga Rendah', 'price_high' => 'Harga Tinggi', 'newest' => 'Terbaru']" />
        </div>
    </x-card>

    <!-- Results Grid -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        @forelse($workers as $worker)
            <x-card>
                <div class="space-y-2">
                    <div class="font-semibold text-lg">{{ $worker->name }}</div>
                    <div class="text-sm text-gray-600">{{ $worker->category_name }}</div>
                    
                    <div class="flex items-center gap-1 text-sm">
                        <span class="text-yellow-500">★</span>
                        <span>{{ number_format($worker->rating, 1) }}</span>
                        <span class="text-gray-600">({{ $worker->review_count }})</span>
                    </div>

                    <div class="text-sm line-clamp-3 text-gray-600">
                        {{ $worker->description }}
                    </div>

                    <div class="border-t pt-2 flex justify-between items-center">
                        <div class="text-sm">
                            Mulai dari <span class="font-semibold">Rp {{ number_format($worker->min_price_idr, 0, ',', '.') }}</span>
                        </div>
                        <a href="{{ route('checkout', ['worker' => $worker->id]) }}" class="text-blue-600 hover:underline text-sm font-medium">
                            Pesan
                        </a>
                    </div>
                </div>
            </x-card>
        @empty
            <div class="col-span-full text-center py-8 text-gray-600">
                <div class="text-4xl mb-2">☹️</div>
                <p>Tidak ada hasil yang sesuai. Coba ubah filter pencarian Anda.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="flex justify-center">
        {{ $paginator->links() }}
    </div>
</div>

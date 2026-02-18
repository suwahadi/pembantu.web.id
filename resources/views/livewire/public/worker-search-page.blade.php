<div>
    <div class="mb-8">
        <h1 class="text-2xl font-bold tracking-tight text-gray-900 dark:text-white sm:text-3xl">Cari Tenaga Kerja</h1>
        <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">Temukan asisten rumah tangga, sopir, atau pengasuh terbaik untuk kebutuhan Anda.</p>
    </div>

    <!-- Filters -->
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4 mb-8">
        <!-- Search Query -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <x-icon.search class="h-4 w-4" />
            </div>
            <input type="text" wire:model.live.debounce.300ms="q" 
                placeholder="Cari nama atau keahlian..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-800 focus:ring-2 focus:ring-blue-500 text-sm">
        </div>

        <!-- Category -->
        <select wire:model.live="category" 
            class="block w-full px-3 py-2 border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-800 focus:ring-2 focus:ring-blue-500 text-sm">
            <option value="">Semua Kategori</option>
            @foreach($categories as $c)
                <option value="{{ $c->slug }}">{{ $c->name }}</option>
            @endforeach
        </select>

        <!-- Location -->
        <select wire:model.live="location" 
            class="block w-full px-3 py-2 border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-800 focus:ring-2 focus:ring-blue-500 text-sm">
            <option value="">Semua Lokasi</option>
            @foreach($locations as $l)
                <option value="{{ $l->slug }}">{{ $l->name }}</option>
            @endforeach
        </select>

        <!-- Sort -->
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-gray-400">
                <x-icon.sort class="h-4 w-4" />
            </div>
            <select wire:model.live="sort" 
                class="block w-full pl-10 pr-3 py-2 border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-gray-800 focus:ring-2 focus:ring-blue-500 text-sm">
                <option value="relevance">Terpopuler (Rating)</option>
                <option value="newest">Terbaru</option>
                <option value="price_asc">Harga Terendah</option>
                <option value="price_desc">Harga Tertinggi</option>
            </select>
        </div>
    </div>

    <!-- Results Grid -->
    @if($items->isEmpty())
        <div class="py-20 text-center">
            <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-gray-50 dark:bg-gray-900 mb-4">
                <x-icon.search class="h-8 w-8 text-gray-400" />
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Tidak ditemukan</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Coba ubah kata kunci atau filter pencarian Anda.</p>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
            @foreach($items as $worker)
                <x-worker-card :worker="$worker" />
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-12">
            {{ $items->links() }}
        </div>
    @endif
</div>

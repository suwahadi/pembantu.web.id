@props(['worker'])

<div class="group relative flex flex-col overflow-hidden rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-gray-900 transition-all hover:shadow-lg">
    <!-- Image -->
    <div class="aspect-[4/3] w-full overflow-hidden bg-gray-100 dark:bg-gray-800">
        @if($worker->photo_path)
            <img src="{{ str_starts_with($worker->photo_path, 'http') ? $worker->photo_path : Storage::url($worker->photo_path) }}" 
                 alt="{{ $worker->name }}" 
                 class="h-full w-full object-cover group-hover:scale-105 transition-transform duration-300">
        @else
            <div class="flex h-full w-full items-center justify-center text-gray-400">
                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
            </div>
        @endif
        
        <div class="absolute top-3 right-3">
            <span class="inline-flex items-center rounded-lg bg-white/90 dark:bg-gray-950/90 px-2 py-1 text-xs font-medium text-blue-600 dark:text-blue-400 backdrop-blur shadow-sm border border-gray-200 dark:border-gray-800">
                {{ $worker->public_id }}
            </span>
        </div>
    </div>

    <!-- Content -->
    <div class="flex flex-1 flex-col p-4">
        <div class="flex items-center justify-between mb-1">
            <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400">
                {{ $worker->category_name ?? $worker->category->name }}
            </span>
            <div class="flex items-center gap-1 text-xs font-medium text-orange-500">
                <svg class="h-3 w-3 fill-current" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" /></svg>
                <span>{{ number_format($worker->rating, 1) }}</span>
            </div>
        </div>

        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1">
            <a href="{{ route('worker.show', $worker->public_id) }}">
                <span class="absolute inset-0"></span>
                {{ $worker->name }}
            </a>
        </h3>

        <div class="flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 mb-4">
            <x-icon.map-pin class="h-3 w-3" />
            <span>{{ $worker->primaryServiceArea?->location?->city ?? '-' }}</span>
        </div>

        <div class="mt-auto flex items-end justify-between">
            <div>
                <p class="text-[10px] text-gray-500 dark:text-gray-400">Mulai dari</p>
                <p class="text-lg font-bold text-gray-900 dark:text-white">
                    Rp {{ number_format($worker->min_price ?? 0, 0, ',', '.') }}
                </p>
            </div>
            <div class="h-8 w-8 rounded-full border border-gray-200 dark:border-gray-800 flex items-center justify-center text-gray-400 group-hover:text-blue-500 group-hover:border-blue-500 transition-colors">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            </div>
        </div>
    </div>
</div>

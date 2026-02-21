<div class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:gap-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">{{ $title }}</p>
                <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-2">{{ $value }}</p>
                @if(isset($change))
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    <span class="{{ $change > 0 ? 'text-success-600 dark:text-success-500' : 'text-error-600 dark:text-error-500' }}">
                        {{ $change > 0 ? '+' : '' }}{{ $change }}%
                    </span>
                    {{ $change > 0 ? 'dari bulan lalu' : 'dari bulan lalu' }}
                </p>
                @endif
            </div>
            <div class="flex items-center justify-center w-12 h-12 {{ $bgColor }} rounded-xl">
                {{ $icon }}
            </div>
        </div>
    </div>
</div>

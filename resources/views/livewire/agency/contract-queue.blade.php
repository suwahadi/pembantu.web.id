<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Kontrak Menunggu Tanda Tangan</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Review dan tandatangani kontrak dari visitor</p>
        </div>

        @if($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada kontrak menunggu tanda tangan</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Worker</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Skema</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Dibuat</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($items as $item)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $item->worker_name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->scheme }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                    {{ \Carbon\Carbon::parse($item->start_date)->format('d/m') }} -
                                    {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Rp {{ number_format($item->total_idr, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                    {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button wire:click="select({{ $item->id }})" class="px-3 py-1.5 rounded-lg text-xs font-medium bg-primary-50 text-primary-600 border border-primary-200 hover:bg-primary-100 hover:text-primary-700 dark:bg-primary-600 dark:text-white dark:border-primary-600 dark:hover:bg-primary-700 dark:hover:text-white transition-colors">
                                        Review
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(method_exists($items, 'links') && $items->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $items->links() }}
                </div>
            @endif
        @endif
    </div>

    @if($selected)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Detail Kontrak</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Tanda tangani kontrak ini untuk menerima order</p>
            </div>

            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Worker</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $selected->worker_name ?? '-' }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Skema</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $selected->scheme }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Mulai</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($selected->start_date)->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Selesai</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($selected->end_date)->translatedFormat('l, d F Y') }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Total</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">Rp {{ number_format($selected->total_idr, 0, ',', '.') }}</span>
                    </div>
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                        <span class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Durasi Jam</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $selected->estimated_hours ?? '-' }} jam</span>
                    </div>
                </div>

                <div class="p-4 rounded-lg bg-gray-50 dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 mb-2">Deskripsi Pekerjaan</div>
                    <div class="whitespace-pre-wrap text-sm text-gray-700 dark:text-gray-300 max-h-64 overflow-auto">{{ $selected->description ?? '-' }}</div>
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="sign" wire:loading.attr="disabled"
                        class="px-5 py-2.5 rounded-lg font-medium text-sm border border-primary-600 bg-gray-900 text-white hover:bg-gray-800 disabled:bg-gray-400 disabled:border-gray-400 disabled:text-gray-200 dark:bg-white dark:text-gray-900 dark:border-gray-300 dark:hover:bg-gray-100 dark:disabled:bg-gray-300 dark:disabled:text-gray-600 transition-colors">
                        <span wire:loading.remove wire:target="sign">Tandatangani Kontrak</span>
                        <span wire:loading wire:target="sign">Memproses...</span>
                    </button>
                    <button wire:click="$set('selectedId', null)"
                        class="px-5 py-2.5 rounded-lg font-medium text-sm border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:bg-gray-100 dark:disabled:bg-gray-800 disabled:text-gray-400 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

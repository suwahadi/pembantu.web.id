<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 md:p-6">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Queue Payout</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $payouts->total() }} payout menunggu diproses</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-lg p-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        @if($payouts->count())
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agency</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rekening</th>
                            <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($payouts as $payout)
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono font-medium text-primary-600 dark:text-primary-400">#{{ $payout->id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">#{{ $payout->order_id }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $payout->agency->company_name ?? $payout->agency->name ?? '-' }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white text-right">Rp {{ number_format($payout->amount_idr, 0, ',', '.') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $payout->status === 'queued' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400' : 'bg-blue-100 text-blue-800 dark:bg-blue-500/20 dark:text-blue-400' }}">
                                        {{ ucfirst($payout->status) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500 dark:text-gray-400">
                                    @if($payout->bankAccount)
                                        {{ $payout->bankAccount->bank_name }} - {{ $payout->bankAccount->account_no }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <button wire:click="selectPayout({{ $payout->id }})"
                                        class="text-sm font-medium px-3 py-1 rounded-lg bg-green-600 text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-colors">
                                        Proses
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($payouts->hasPages())
                <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                    {{ $payouts->links() }}
                </div>
            @endif
        @else
            <div class="py-12 text-center">
                <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada payout yang perlu diproses</p>
            </div>
        @endif
    </div>

    @if($selectedPayoutId)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Proses Payout #{{ $selectedPayoutId }}</h3>
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Transfer</label>
                    <input type="date" wire:model.live="transferDate"
                        class="w-full max-w-xs h-10 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Bukti Transfer</label>
                    <input type="file" wire:model="proofFile"
                        class="block w-full max-w-md text-sm text-gray-900 dark:text-gray-300 border border-gray-200 dark:border-gray-600 rounded-lg p-2 bg-white dark:bg-gray-800 file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-sm file:bg-primary-50 file:text-primary-600 dark:file:bg-primary-500/20 dark:file:text-primary-400">
                    @if ($proofFile)
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1">File dipilih: {{ $proofFile->getClientOriginalName() }}</p>
                    @endif
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="markPaid"
                        class="px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                        Mark Paid
                    </button>
                    <button wire:click="resetForm"
                        class="px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

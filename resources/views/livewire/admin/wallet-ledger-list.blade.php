<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Wallet Ledger</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola dan pantau semua transaksi keuangan sistem</p>
            </div>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="grid grid-cols-12 gap-4 md:gap-6 mb-6">
        <div class="col-span-12 md:col-span-6 lg:col-span-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Transaksi</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-2">{{ number_format($totalCount ?? 0) }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl">
                        <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-span-12 md:col-span-6 lg:col-span-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Nilai</p>
                        <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-2">Rp {{ number_format($totalDebit ?? 0, 0, ',', '.') }}</p>
                    </div>
                    <div class="flex items-center justify-center w-12 h-12 bg-green-100 dark:bg-green-500/20 rounded-xl">
                        <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] mb-6">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end">
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari</label>
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari entry key, account, deskripsi..." 
                    class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white">
            </div>
            <div class="w-full lg:w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Account</label>
                <select wire:model.live="accountFilter" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Account</option>
                    @foreach($accounts as $account)
                        <option value="{{ $account }}">{{ $account }}</option>
                    @endforeach
                </select>
            </div>
            <div class="w-full lg:w-48">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Filter Tipe Ref</label>
                <select wire:model.live="refTypeFilter" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    <option value="">Semua Tipe</option>
                    @foreach($refTypes as $type)
                        <option value="{{ $type }}">{{ ucfirst($type) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Ledger Table -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03] overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Entry Key</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Tipe</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Debit Account</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Credit Account</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase text-right">Amount</th>
                        <th class="px-4 py-3 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($ledgers as $ledger)
                        @php
                            $refBadgeColors = [
                                'order' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                'refund' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
                                'payout' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                'escrow' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                            ];
                            $refColor = $refBadgeColors[$ledger->ref_type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300';
                        @endphp
                        <tr class="">
                            <td class="px-4 py-3">
                                <div class="text-sm font-mono text-gray-900 dark:text-white">{{ Str::limit($ledger->entry_key, 35) }}</div>
                                @if($ledger->description)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($ledger->description, 50) }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $refColor }}">
                                    {{ ucfirst($ledger->ref_type) }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-mono text-red-600 dark:text-red-400">-{{ $ledger->debit_account }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm font-mono text-green-600 dark:text-green-400">+{{ $ledger->credit_account }}</span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($ledger->amount_idr, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="text-sm text-gray-500 dark:text-gray-400">{{ $ledger->created_at->format('d M Y H:i') }}</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 mb-4">
                                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                </div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada transaksi</h3>
                                <p class="text-gray-500 dark:text-gray-400 mt-1">Tidak ada data wallet ledger yang ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($ledgers->hasPages())
            <div class="px-4 py-3 border-t border-gray-200 dark:border-gray-700">
                {{ $ledgers->links() }}
            </div>
        @endif
    </div>
</div>

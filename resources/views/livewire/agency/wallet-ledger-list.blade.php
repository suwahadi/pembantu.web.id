<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Page Header -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Wallet Ledger</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Pantau semua transaksi keuangan agency Anda</p>
        </div>

        @if(!$agency)
            <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-700 mb-4">
                    <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Agency Tidak Ditemukan</h3>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Anda belum terdaftar sebagai agency.</p>
            </div>
        @else
            <!-- Status Notification -->
            @if($statusMessage)
                <div id="status-notification" class="mb-6 rounded-xl p-4 {{ $statusType === 'success' ? 'bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800' : 'bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800' }}">
                    <div class="flex items-start gap-3">
                        @if($statusType === 'success')
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        @endif
                        <div class="flex-1">
                            <p class="text-sm font-medium {{ $statusType === 'success' ? 'text-green-800 dark:text-green-200' : 'text-red-800 dark:text-red-200' }}">
                                {{ $statusMessage }}
                            </p>
                        </div>
                        <button wire:click="$set('statusMessage', '')" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <!-- Current Balance -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Saldo Wallet</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ number_format($balance, 0, ',', '.') }}</p>
                        </div>
                        <div class="h-12 w-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-green-600 dark:text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Earned -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Pendapatan</p>
                            <p class="text-2xl font-bold text-blue-600 dark:text-blue-400 mt-2">Rp {{ number_format($totalEarned, 0, ',', '.') }}</p>
                        </div>
                        <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" />
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Total Paid -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Payout</p>
                            <p class="text-2xl font-bold text-purple-600 dark:text-purple-400 mt-2">Rp {{ number_format($totalPaid, 0, ',', '.') }}</p>
                        </div>
                        <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
                            <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Payout Request Button & Form -->
            <div class="mb-6">
                @if(!$showPayoutForm)
                    <button wire:click="togglePayoutForm" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Ajukan Payout
                    </button>
                @else
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6" id="payout-form">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Form Pengajuan Payout</h3>
                            <button wire:click="togglePayoutForm" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        @if($bankAccounts->isEmpty())
                            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 mb-4">
                                <div class="flex items-start gap-3">
                                    <svg class="w-5 h-5 text-amber-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                    <div>
                                        <p class="text-sm font-medium text-amber-800">Rekening Bank Belum Ada</p>
                                        <p class="text-sm text-amber-700 mt-1">Anda perlu menambahkan rekening bank terverifikasi terlebih dahulu.</p>
                                        <a href="{{ route('agency.bank-accounts') }}" class="inline-flex items-center gap-1 mt-2 text-sm font-medium text-amber-800 hover:underline">
                                            Kelola Rekening Bank
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @else
                            <form wire:submit.prevent="submitPayoutRequest" x-data="{ confirmDialog: false }">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Jumlah Payout (Rp) <span class="text-red-500">*</span></label>
                                        <input type="number" wire:model="payoutAmount" min="10000" step="1000" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-white" placeholder="Minimal Rp 10.000">
                                        @error('payoutAmount')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Minimal: Rp 10.000 | Saldo: Rp {{ number_format($balance, 0, ',', '.') }}</p>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Rekening Bank Tujuan <span class="text-red-500">*</span></label>
                                        <select wire:model="selectedBankAccountId" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                                            <option value="">Pilih Rekening Bank</option>
                                            @foreach($bankAccounts as $account)
                                                <option value="{{ $account->id }}">{{ $account->bank_name }} - {{ $account->account_no }} ({{ $account->account_name }})</option>
                                            @endforeach
                                        </select>
                                        @error('selectedBankAccountId')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                    </div>
                                </div>
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Catatan (Opsional)</label>
                                    <textarea wire:model="payoutNote" rows="2" class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-purple-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-white" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                                    @error('payoutNote')<p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>@enderror
                                </div>
                                <div class="flex items-center gap-3">
                                    <button type="button" x-on:click="confirmDialog = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors shadow-sm">Konfirmasi & Ajukan</button>
                                    <button type="button" wire:click="togglePayoutForm" class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Batal</button>
                                </div>
                                <div x-show="confirmDialog" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50">
                                    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="h-12 w-12 rounded-full bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <div>
                                                <h4 class="text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Payout</h4>
                                                <p class="text-sm text-gray-500 dark:text-gray-400">Pastikan data sudah benar</p>
                                            </div>
                                        </div>
                                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 mb-6 space-y-2">
                                            <div class="flex justify-between text-sm"><span class="text-gray-500 dark:text-gray-400">Jumlah:</span><span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($payoutAmount, 0, ',', '.') }}</span></div>
                                            <div class="flex justify-between text-sm"><span class="text-gray-500 dark:text-gray-400">Rekening:</span><span class="font-medium text-gray-900 dark:text-white">@php $selectedAccount = $bankAccounts->firstWhere('id', $selectedBankAccountId); @endphp{{ $selectedAccount ? $selectedAccount->bank_name : '-' }}</span></div>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <button type="submit" x-on:click="confirmDialog = false" class="flex-1 inline-flex justify-center items-center gap-2 px-4 py-2.5 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 transition-colors">Ya, Ajukan Payout</button>
                                            <button type="button" x-on:click="confirmDialog = false" class="flex-1 px-4 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">Batal</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        @endif
                    </div>
                @endif
            </div>

            <!-- Search Filter -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Cari Transaksi</label>
                        <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari entry key atau deskripsi..." 
                            class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-900 dark:text-white">
                    </div>
                </div>
            </div>

            <!-- Ledger Table -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="">
                            <tr>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Transaksi</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Tipe</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase text-right">Jumlah</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase">Waktu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                            @forelse($ledgers as $ledger)
                                @php
                                    $isCredit = str_contains($ledger->credit_account, 'agency_' . $agency->id);
                                    $isDebit = str_contains($ledger->debit_account, 'agency_' . $agency->id);
                                    
                                    $typeColors = [
                                        'order' => 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
                                        'refund' => 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-300',
                                        'payout' => 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
                                        'escrow' => 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300',
                                    ];
                                    $typeColor = $typeColors[$ledger->ref_type] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-mono text-gray-900 dark:text-white">{{ Str::limit($ledger->entry_key, 30) }}</div>
                                        @if($ledger->description)
                                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit($ledger->description, 40) }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $typeColor }}">
                                            {{ ucfirst($ledger->ref_type) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        @if($isCredit)
                                            <span class="text-sm font-semibold text-green-600 dark:text-green-400">+Rp {{ number_format($ledger->amount_idr, 0, ',', '.') }}</span>
                                        @elseif($isDebit)
                                            <span class="text-sm font-semibold text-red-600 dark:text-red-400">-Rp {{ number_format($ledger->amount_idr, 0, ',', '.') }}</span>
                                        @else
                                            <span class="text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($ledger->amount_idr, 0, ',', '.') }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm text-gray-500 dark:text-gray-400">{{ $ledger->created_at->format('d M Y H:i') }}</span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-700 mb-4">
                                            <svg class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada transaksi</h3>
                                        <p class="text-gray-500 dark:text-gray-400 mt-1">Tidak ada data transaksi yang ditemukan.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($ledgers->hasPages())
                    <div class="px-6 py-4 border-t border-gray-100 dark:border-gray-700">
                        {{ $ledgers->links() }}
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Auto-scroll to notification on status update -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('payout-status-updated', () => {
                setTimeout(() => {
                    const notification = document.getElementById('status-notification');
                    if (notification) {
                        notification.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            });
        });
    </script>
</div>

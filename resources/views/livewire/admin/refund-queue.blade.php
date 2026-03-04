<div class="space-y-4">
    <!-- Flash Messages -->
    @if(session('success'))
        <div id="status-notification" class="rounded-xl p-4 bg-green-50 border border-green-200 dark:bg-green-900/20 dark:border-green-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium text-green-800 dark:text-green-200">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div id="status-notification" class="rounded-xl p-4 bg-red-50 border border-red-200 dark:bg-red-900/20 dark:border-red-800">
            <div class="flex items-start gap-3">
                <svg class="w-5 h-5 text-red-600 dark:text-red-400 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-sm font-medium text-red-800 dark:text-red-200">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Header -->
    <x-card class="dark:bg-gray-900/60 dark:border-gray-700">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="font-semibold text-lg dark:text-white">Queue Refund</div>
                <p class="text-sm text-gray-600 dark:text-gray-400">Menampilkan {{ $refunds->total() }} refund yang sesuai filter.</p>
            </div>
            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row sm:items-center">
                <div>
                    <select
                        id="refund-status-filter"
                        wire:model.live="statusFilter"
                        class="w-full rounded-full border border-gray-200 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200"
                    >
                        <option value="all">Semua Status</option>
                        <option value="queued">Queued</option>
                        <option value="processing">Processing</option>
                        <option value="paid">Paid</option>
                        <option value="failed">Failed</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <div class="relative">
                    <input
                        id="refund-search"
                        type="text"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Cari Data"
                        class="w-full rounded-full border border-gray-200 bg-white text-sm text-gray-700 transition hover:border-gray-300 focus:border-gray-400 focus:outline-none dark:border-gray-700 dark:bg-gray-900 dark:text-gray-200 sm:w-72"
                    />
                </div>
            </div>
        </div>
    </x-card>

    <!-- Refunds Table -->
    <x-card class="dark:bg-gray-900/60 dark:border-gray-700">
        @if($refunds->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b bg-gray-50 dark:bg-gray-800 dark:border-gray-700">
                        <tr>
                            <th class="text-left py-3 px-4 text-gray-900 dark:text-white font-semibold">Refund ID</th>
                            <th class="text-left py-3 px-4 text-gray-900 dark:text-white font-semibold">Order ID</th>
                            <th class="text-left py-3 px-4 text-gray-900 dark:text-white font-semibold">Customer</th>
                            <th class="text-right py-3 px-4 text-gray-900 dark:text-white font-semibold">Amount</th>
                            <th class="text-left py-3 px-4 text-gray-900 dark:text-white font-semibold">Status</th>
                            <th class="text-left py-3 px-4 text-gray-900 dark:text-white font-semibold">Bank Account</th>
                            <th class="text-center py-3 px-4 text-gray-900 dark:text-white font-semibold">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($refunds as $refund)
                            @php
                                $statusColors = [
                                    'queued' => 'bg-blue-600',
                                    'processing' => 'bg-blue-500',
                                    'paid' => 'bg-green-500',
                                    'failed' => 'bg-red-500',
                                ];
                            @endphp
                            <tr class="">
                                <td class="py-3 px-4 font-mono text-blue-600 dark:text-blue-400">
                                    #{{ $refund->id }}
                                    @if(!$refund->order_id)
                                        <span class="text-xs text-gray-500 block">Wallet</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 font-mono text-gray-900 dark:text-gray-300">
                                    @if($refund->order_id && $refund->order)
                                        <a href="{{ route('admin.orders.show', $refund->order_id) }}" class="text-blue-600 hover:underline">
                                            #{{ $refund->order?->code ?? $refund->order_id }}
                                        </a>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-gray-900 dark:text-gray-300">
                                    @if($refund->order?->visitor)
                                        <div class="font-medium">{{ $refund->order->visitor->name }}</div>
                                        <div class="text-xs text-gray-500">{{ $refund->order->visitor->email }}</div>
                                    @elseif($refund->payee_type === 'USER' && $refund->payee_id)
                                        @php
                                            $payeeUser = \App\Models\User::find($refund->payee_id);
                                        @endphp
                                        @if($payeeUser)
                                            <div class="font-medium">{{ $payeeUser->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $payeeUser->email }}</div>
                                            <span class="text-xs text-amber-600">(Wallet Withdrawal)</span>
                                        @else
                                            -
                                        @endif
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-right font-medium text-gray-900 dark:text-gray-300">
                                    Rp {{ number_format($refund->amount_idr, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold text-white {{ $statusColors[$refund->status] ?? 'bg-gray-600' }}">
                                        {{ ucfirst($refund->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-600 dark:text-gray-400">
                                    @if($refund->bankAccount)
                                        <div class="font-medium">{{ $refund->bankAccount->bank_name }}</div>
                                        <div>{{ $refund->bankAccount->account_no }}</div>
                                        <div class="text-gray-500">{{ $refund->bankAccount->account_name }}</div>
                                    @else
                                        <span class="text-gray-400">-</span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-center">
                                    @if(in_array($refund->status, ['queued', 'failed']))
                                        <button wire:click="selectRefund({{ $refund->id }})"
                                            class="text-sm font-medium px-3 py-1 rounded-lg bg-green-600 text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-colors">
                                            Proses
                                        </button>
                                    @elseif($refund->status === 'paid')
                                        <span class="text-xs text-green-600 dark:text-green-400 font-medium">Sudah Dibayar</span>
                                    @elseif($refund->status === 'processing')
                                        <span class="text-xs text-blue-600 dark:text-blue-400 font-medium">Sedang Diproses</span>
                                    @elseif($refund->status === 'cancelled')
                                        <span class="text-xs text-gray-500 dark:text-gray-400">Dibatalkan</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $refunds->links() }}</div>
        @else
            <p class="text-center text-gray-600 dark:text-gray-400 py-8">Tidak ada refund yang perlu diproses.</p>
        @endif
    </x-card>

    <!-- Process Form -->
    @if($selectedRefundId)
        <x-card title="Proses Refund #{{ $selectedRefundId }}" class="dark:bg-gray-900/60 dark:border-gray-700">
            <form wire:submit.prevent="markPaid" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Tanggal Transfer</label>
                    <input type="date" wire:model="transferDate" required
                        class="w-full rounded-lg border border-gray-300 px-3 py-2 text-sm focus:border-blue-500 focus:outline-none dark:border-gray-600 dark:bg-gray-800 dark:text-white">
                    @error('transferDate')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Bukti Transfer <span class="text-red-500">*</span>
                    </label>
                    <input type="file" wire:model="proofFile" accept="image/*,.pdf" required
                        class="block w-full text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg p-2" />
                    @if ($proofFile)
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center gap-1">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            File dipilih: {{ $proofFile->getClientOriginalName() }}
                        </p>
                    @endif
                    @error('proofFile')<p class="mt-1 text-xs text-red-600">{{ $message }}</p>@enderror
                </div>

                <div class="flex gap-2 pt-4">
                    <button type="submit" wire:loading.attr="disabled" wire:target="markPaid"
                        class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="markPaid">Mark Paid</span>
                        <span wire:loading wire:target="markPaid">Memproses...</span>
                    </button>
                    <button type="button" wire:click="resetForm"
                        class="px-5 py-2.5 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-700 transition-colors">
                        Batal
                    </button>
                </div>
            </form>
        </x-card>
    @endif

    <!-- Auto-scroll to notification on page update -->
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('refund-processed', () => {
                setTimeout(() => {
                    const notification = document.getElementById('status-notification');
                    if (notification) {
                        notification.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                }, 100);
            });
        });

        // Auto-scroll on initial load if notification exists
        document.addEventListener('DOMContentLoaded', () => {
            const notification = document.getElementById('status-notification');
            if (notification) {
                notification.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        });
    </script>
</div>

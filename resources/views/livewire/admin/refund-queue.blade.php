<div class="space-y-4">
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
                                </td>
                                <td class="py-3 px-4 font-mono text-gray-900 dark:text-gray-300">
                                    #{{ $refund->order_id }}
                                </td>
                                <td class="py-3 px-4 text-gray-900 dark:text-gray-300">{{ $refund->order?->visitor?->name ?? '-' }}</td>
                                <td class="py-3 px-4 text-right font-medium text-gray-900 dark:text-gray-300">
                                    Rp {{ number_format($refund->amount_idr, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold text-white {{ $statusColors[$refund->status] ?? 'bg-gray-600' }}">
                                        {{ ucfirst($refund->status) }}
                                    </span>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-600 dark:text-gray-400">
                                    {{ $refund->bankAccount?->account_number ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button wire:click="selectRefund({{ $refund->id }})"
                                        class="text-sm font-medium px-3 py-1 rounded-lg bg-green-600 text-white hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600 transition-colors">
                                        Proses
                                    </button>
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
        <x-card title="Proses Refund" class="dark:bg-gray-900/60 dark:border-gray-700">
            <div class="space-y-4">
                <x-form.input type="date" label="Tanggal Transfer" wire:model.live="transferDate" class="dark:text-white" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Bukti Transfer</label>
                    <input type="file" wire:model="proofFile" class="block w-full text-sm border border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-white rounded-lg p-2" />
                    @if ($proofFile)
                        <p class="text-xs text-green-600 dark:text-green-400 mt-1 flex items-center gap-1">
                          @include('svgs.icon-check', ['class' => 'w-4 h-4 text-green-600 dark:text-green-400'])
                          File dipilih: {{ $proofFile->getClientOriginalName() }}
                        </p>
                    @endif
                </div>

                <div class="flex gap-2 pt-4">
                    <x-button wire:click="markPaid">Mark Paid</x-button>
                    <x-button variant="secondary" wire:click="resetForm">Batal</x-button>
                </div>
            </div>
        </x-card>
    @endif
</div>

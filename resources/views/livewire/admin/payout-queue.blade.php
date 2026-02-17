<div class="space-y-4">
    <!-- Header -->
    <x-card>
        <div class="flex justify-between items-center">
            <div>
                <div class="font-semibold text-lg">Queue Payout</div>
                <p class="text-sm text-gray-600">Process {{ $payouts->total() }} pending payouts</p>
            </div>
        </div>
    </x-card>

    <!-- Payouts Table -->
    <x-card>
        @if($payouts->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4">Payout ID</th>
                            <th class="text-left py-3 px-4">Order ID</th>
                            <th class="text-left py-3 px-4">Agency</th>
                            <th class="text-right py-3 px-4">Amount</th>
                            <th class="text-left py-3 px-4">Status</th>
                            <th class="text-left py-3 px-4">Bank Account</th>
                            <th class="text-center py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($payouts as $payout)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-mono text-blue-600">
                                    #{{ $payout->id }}
                                </td>
                                <td class="py-3 px-4 font-mono">
                                    #{{ $payout->order_id }}
                                </td>
                                <td class="py-3 px-4">{{ $payout->agency->name }}</td>
                                <td class="py-3 px-4 text-right font-medium">
                                    Rp {{ number_format($payout->amount_idr, 0, ',', '.') }}
                                </td>
                                <td class="py-3 px-4">
                                    <x-badge variant="{{ $payout->status === 'queued' ? 'yellow' : 'blue' }}">
                                        {{ ucfirst($payout->status) }}
                                    </x-badge>
                                </td>
                                <td class="py-3 px-4 text-xs text-gray-600">
                                    {{ $payout->bankAccount?->account_number ?? '-' }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button wire:click="selectPayout({{ $payout->id }})"
                                        class="text-blue-600 hover:underline text-xs font-medium">
                                        Proses
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $payouts->links() }}</div>
        @else
            <p class="text-center text-gray-600 py-8">Tidak ada payout yang perlu diproses.</p>
        @endif
    </x-card>

    <!-- Process Form -->
    @if($selectedPayoutId)
        <x-card title="Proses Payout">
            <div class="space-y-4">
                <x-form.input type="date" label="Tanggal Transfer" wire:model.live="transferDate" />

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bukti Transfer</label>
                    <input type="file" wire:model="proofFile" class="block w-full text-sm border border-gray-300 rounded-lg p-2" />
                    @if ($proofFile)
                        <p class="text-xs text-green-600 mt-1">✓ File dipilih: {{ $proofFile->getClientOriginalName() }}</p>
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

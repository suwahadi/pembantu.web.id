<div class="space-y-4">
    <!-- Header -->
    <x-card>
        <div class="flex justify-between items-center">
            <div>
                <div class="font-semibold text-lg">Queue Dispute</div>
                <p class="text-sm text-gray-600">Resolve {{ count($disputes) }} open disputes</p>
            </div>
        </div>
    </x-card>

    <!-- Disputes Table -->
    <x-card>
        @if($disputes->count())
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="border-b bg-gray-50">
                        <tr>
                            <th class="text-left py-3 px-4">Order ID</th>
                            <th class="text-left py-3 px-4">Visitor</th>
                            <th class="text-left py-3 px-4">Agency</th>
                            <th class="text-left py-3 px-4">Reason</th>
                            <th class="text-left py-3 px-4">Dibuat</th>
                            <th class="text-center py-3 px-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach($disputes as $dispute)
                            <tr class="hover:bg-gray-50">
                                <td class="py-3 px-4 font-mono text-blue-600">
                                    #{{ $dispute->order_id }}
                                </td>
                                <td class="py-3 px-4">{{ $dispute->order->visitor->name }}</td>
                                <td class="py-3 px-4">{{ $dispute->order->agency->name }}</td>
                                <td class="py-3 px-4 text-xs">{{ $dispute->reason }}</td>
                                <td class="py-3 px-4 text-xs text-gray-600">
                                    {{ $dispute->created_at->format('d M Y H:i') }}
                                </td>
                                <td class="py-3 px-4 text-center">
                                    <button wire:click="selectDispute({{ $dispute->id }}, 'view')"
                                        class="text-blue-600 hover:underline text-xs font-medium">
                                        Lihat
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-4">{{ $disputes->links() }}</div>
        @else
            <p class="text-center text-gray-600 py-8">Tidak ada dispute yang perlu ditangani.</p>
        @endif
    </x-card>

    <!-- Resolution Form -->
    @if($selectedDisputeId)
        <x-card title="Resolusi Dispute">
            <div class="space-y-4">
                <x-form.textarea label="Catatan" wire:model.live="resolutionNotes" :rows="3"
                    placeholder="Jelaskan keputusan Anda..." />

                @if($selectedAction === 'partial')
                    <x-form.input type="number" label="Jumlah Refund (IDR)" wire:model.live="refundAmount"
                        placeholder="Berapa yang akan di-refund?" />
                @endif

                <div class="flex gap-2 pt-4">
                    @if($selectedAction !== 'partial')
                        <x-button variant="danger" wire:click="resolveFullRefund">
                            Refund Penuh
                        </x-button>
                        <x-button wire:click="resolveFullRelease">
                            Release Penuh
                        </x-button>
                    @else
                        <x-button wire:click="resolvePartial">
                            Partial Split
                        </x-button>
                    @endif
                    <x-button variant="secondary" wire:click="resetForm">Batal</x-button>
                </div>
            </div>
        </x-card>
    @endif
</div>

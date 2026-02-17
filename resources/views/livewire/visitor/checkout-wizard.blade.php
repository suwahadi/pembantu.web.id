<div class="max-w-3xl mx-auto space-y-4">
    <!-- Progress -->
    <x-card>
        <div class="flex items-center justify-between">
            <div>
                <div class="font-semibold text-lg">Checkout</div>
                <div class="text-sm text-gray-600">Langkah {{ $step }} dari 4</div>
            </div>
            <div class="flex gap-1">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="w-10 h-10 rounded-full flex items-center justify-center font-medium text-sm
                        {{ $i <= $step ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                        {{ $i }}
                    </div>
                @endfor
            </div>
        </div>
    </x-card>

    <!-- Step 1: Dates & Scheme -->
    @if($step === 1)
        <x-card>
            <div class="space-y-4">
                <div>
                    <div class="font-semibold mb-3">1. Pilih Tanggal & Skema</div>
                </div>

                <x-form.select label="Skema" wire:model.live="scheme" :options="$schemes" />

                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                    <x-form.input type="date" label="Tanggal Mulai" wire:model.live="startDate" />
                    <x-form.input type="date" label="Tanggal Selesai" wire:model.live="endDate" />
                </div>

                <div class="flex justify-end gap-2 pt-4">
                    <x-button variant="secondary" onclick="window.history.back()">Batal</x-button>
                    <x-button wire:click="next">Lanjut</x-button>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Step 2: Location & Details -->
    @if($step === 2)
        <x-card>
            <div class="space-y-4">
                <div>
                    <div class="font-semibold mb-3">2. Lokasi & Detail Pekerjaan</div>
                </div>

                <x-form.select label="Lokasi" wire:model.live="locationId" :options="$locations" />
                <x-form.textarea label="Alamat Kerja" wire:model.live="workAddress" :rows="3" />
                <x-form.textarea label="Ruang Lingkup Pekerjaan" wire:model.live="scopeOfWork" :rows="4" />

                <div class="flex justify-between gap-2 pt-4">
                    <x-button variant="secondary" wire:click="back">Kembali</x-button>
                    <x-button wire:click="next">Lanjut</x-button>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Step 3: Review -->
    @if($step === 3)
        <x-card>
            <div class="space-y-4">
                <div>
                    <div class="font-semibold mb-3">3. Review Kontrak</div>
                    <p class="text-sm text-gray-600">Pastikan semua informasi sudah benar sebelum melanjutkan.</p>
                </div>

                <div class="bg-gray-50 rounded-lg p-4 space-y-3 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Skema:</span>
                        <span class="font-medium">{{ $schemes[$scheme] ?? $scheme }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Periode:</span>
                        <span class="font-medium">{{ $startDate }} s/d {{ $endDate }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Alamat:</span>
                        <span class="font-medium text-right max-w-xs">{{ $workAddress }}</span>
                    </div>
                </div>

                <div class="flex justify-between gap-2 pt-4">
                    <x-button variant="secondary" wire:click="back">Kembali</x-button>
                    <x-button wire:click="signAndPay">Tanda Tangan & Bayar</x-button>
                </div>
            </div>
        </x-card>
    @endif

    <!-- Step 4: Payment -->
    @if($step === 4)
        <x-card>
            <div class="space-y-4">
                <div>
                    <div class="font-semibold mb-2">4. Instruksi Pembayaran</div>
                    <p class="text-sm text-gray-600">Arahkan ke Midtrans untuk menyelesaikan pembayaran.</p>
                </div>

                @if($orderId)
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <p class="text-sm text-blue-900">
                            Pesanan Anda telah dibuat (ID: <span class="font-mono">#{{ $orderId }}</span>). 
                            Silakan selesaikan pembayaran untuk memulai proses.
                        </p>
                    </div>

                    @if($paymentInstruction)
                        <button onclick="window.open(this.dataset.url, '_blank')" data-url="{{ $paymentInstruction['redirect_url'] ?? '#' }}"
                            class="w-full bg-blue-600 text-white px-4 py-3 rounded-lg font-medium hover:bg-blue-700">
                            Lanjut ke Pembayaran
                        </button>
                    @endif

                    <a href="{{ route('orders.show', $orderId) }}" class="block text-center text-sm text-blue-600 hover:underline">
                        Lihat Detail Pesanan
                    </a>
                @else
                    <p class="text-sm text-gray-600">Membuat pesanan...</p>
                @endif
            </div>
        </x-card>
    @endif
</div>

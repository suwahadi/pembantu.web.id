<div>
    <div class="max-w-4xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-6">Pilih Metode Pembayaran</h1>

        <!-- Order Summary -->
        <div class="bg-gray-50 p-4 rounded-lg mb-6">
            <h2 class="text-lg font-semibold mb-2">Ringkasan Pesanan</h2>
            <p><strong>Pekerja:</strong> {{ $this->order->worker->name ?? 'N/A' }}</p>
            <p><strong>Total:</strong> Rp {{ number_format($this->order->total_idr) }}</p>
        </div>

        @if(!$this->paymentDetails)
            <!-- Payment Methods -->
            <div class="mb-6">
                <h2 class="text-lg font-semibold mb-4">Metode Pembayaran</h2>

                <!-- Bank Transfer -->
                <div class="mb-4">
                    <h3 class="font-medium mb-2">Transfer Bank</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        @foreach($banks as $bankKey => $bankName)
                            <button
                                wire:click="selectBankTransfer('{{ $bankKey }}')"
                                class="border-2 p-4 rounded-lg text-center hover:border-blue-500 {{ $this->selectedPaymentType === 'bank_transfer' && $this->selectedBank === $bankKey ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}"
                            >
                                <div class="text-2xl mb-2">
                                    @switch($bankKey)
                                        @case('bca')
                                            🏦 BCA
                                            @break
                                        @case('bni')
                                            🏦 BNI
                                            @break
                                        @case('mandiri')
                                            🏦 Mandiri
                                            @break
                                        @case('bri')
                                            🏦 BRI
                                            @break
                                        @case('permata')
                                            🏦 Permata
                                            @break
                                        @default
                                            🏦 {{ $bankName }}
                                    @endswitch
                                </div>
                                <div class="text-sm">{{ $bankName }}</div>
                            </button>
                        @endforeach
                    </div>
                </div>

                <!-- QRIS / GoPay -->
                <div class="mb-4">
                    <h3 class="font-medium mb-2">QRIS</h3>
                    <button
                        wire:click="selectGopay"
                        class="border-2 p-4 rounded-lg text-center hover:border-green-500 {{ $this->selectedPaymentType === 'gopay' ? 'border-green-500 bg-green-50' : 'border-gray-300' }}"
                    >
                        <div class="text-2xl mb-2">📱</div>
                        <div class="text-sm">GoPay / QRIS</div>
                    </button>
                </div>
            </div>

            <!-- Process Button -->
            @if($this->selectedPaymentType)
                <button
                    wire:click="processPayment"
                    wire:loading.attr="disabled"
                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 disabled:opacity-50"
                >
                    <span wire:loading.remove>Proses Pembayaran</span>
                    <span wire:loading>Memproses...</span>
                </button>
            @endif

            @error('payment')
                <p class="text-red-500 mt-4">{{ $message }}</p>
            @enderror
        @else
            <!-- Payment Details -->
            <div class="bg-green-50 p-4 rounded-lg">
                <h2 class="text-lg font-semibold mb-4">Instruksi Pembayaran</h2>

                @if($this->selectedPaymentType === 'bank_transfer')
                    <p><strong>Bank:</strong> {{ strtoupper($this->selectedBank) }}</p>
                    @if(isset($this->paymentDetails['va_numbers']))
                        @foreach($this->paymentDetails['va_numbers'] as $va)
                            <p><strong>Nomor VA:</strong> {{ $va['va_number'] }}</p>
                        @endforeach
                    @endif
                    <p><strong>Jumlah:</strong> Rp {{ number_format($this->order['total_idr']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">Silakan transfer ke rekening di atas dalam waktu 24 jam.</p>
                @elseif($this->selectedPaymentType === 'gopay')
                    @if(isset($this->paymentDetails['actions']))
                        @foreach($this->paymentDetails['actions'] as $action)
                            @if($action['name'] === 'generate-qr-code')
                                <p>Scan QR Code berikut:</p>
                                <img src="{{ $action['url'] }}" alt="QR Code" class="mt-2">
                            @endif
                        @endforeach
                    @endif
                    <p><strong>Jumlah:</strong> Rp {{ number_format($this->order['total_idr']) }}</p>
                    <p class="text-sm text-gray-600 mt-2">Buka aplikasi GoPay atau e-wallet lain yang mendukung QRIS, scan kode di atas.</p>
                @endif

                <div class="mt-4">
                    <a href="{{ route('orders.show', $this->orderId) }}" class="bg-blue-600 text-white px-4 py-2 rounded-lg">Lihat Detail Pesanan</a>
                </div>
            </div>
        @endif
    </div>
</div>

<div
    x-data="{
        copy(text) {
            if (!text) return;

            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).catch(() => {
                    const el = document.createElement('textarea');
                    el.value = text;
                    document.body.appendChild(el);
                    el.select();
                    document.execCommand('copy');
                    document.body.removeChild(el);
                });
            } else {
                const el = document.createElement('textarea');
                el.value = text;
                document.body.appendChild(el);
                el.select();
                document.execCommand('copy');
                document.body.removeChild(el);
            }
        }
    }"
    class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6"
>
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 dark:text-white">
            {{ $this->paymentDetails && !isset($this->paymentDetails['payment_success']) ? 'Instruksi Pembayaran' : ($this->paymentDetails && isset($this->paymentDetails['payment_success']) ? 'Pembayaran Berhasil' : 'Pilih Metode Pembayaran') }}
        </h1>
        <div class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ $this->paymentDetails && isset($this->paymentDetails['payment_success']) ? 'Pembayaran Anda telah berhasil diterima.' : 'Selesaikan pembayaran untuk melanjutkan proses pesanan.' }}
        </div>

        <div class="mt-4 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
            <div class="flex items-center gap-2">
                <button
                    type="button"
                    wire:click="refreshPayment"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center rounded-xl border border-gray-200 bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 disabled:opacity-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800"
                >
                    <span wire:loading.remove wire:target="refreshPayment">Refresh Pembayaran</span>
                    <span wire:loading wire:target="refreshPayment">Memuat...</span>
                </button>

                <a
                    href="{{ route('orders.show', $this->orderId) }}"
                    class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                >
                    Detail Pesanan
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left: Instruksi Pembayaran -->
        <div>
            @if($this->paymentDetails)
                @if(isset($this->paymentDetails['payment_success']) && $this->paymentDetails['payment_success'])
                    <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900/40 dark:bg-emerald-900/20">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">Pembayaran Berhasil</div>
                            <div class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
                                STATUS: <span class="font-semibold">SETTLEMENT</span>
                            </div>
                        </div>

                        <div class="mt-6 rounded-2xl border border-emerald-200 bg-white p-5 dark:border-emerald-900/40 dark:bg-gray-900">
                            <div class="text-center space-y-4">
                                <div class="text-lg font-semibold text-emerald-700 dark:text-emerald-300">
                                    Pembayaran berhasil diterima!
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">
                                    Pesanan Anda telah dibayar dan sedang diproses.
                                </div>
                                
                                <div class="pt-4 border-t border-gray-100 dark:border-gray-800">
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Tanggal Pembayaran</div>
                                            <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">
                                                {{ optional($this->paymentDetails['settled_at'])->format('d M Y, H:i') ?? now()->format('d M Y, H:i') }}
                                            </div>
                                        </div>
                                        <div>
                                            <div class="text-xs text-gray-500 dark:text-gray-400">Jumlah</div>
                                            <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($this->order->total_idr) }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-4">
                                    <a
                                        href="{{ route('orders.show', $this->orderId) }}"
                                        class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700"
                                    >
                                        Lihat Detail Pesanan
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                <div class="rounded-2xl border border-emerald-200 bg-emerald-50 p-5 shadow-sm dark:border-emerald-900/40 dark:bg-emerald-900/20">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div class="text-sm font-semibold text-gray-900 dark:text-white">Instruksi Pembayaran</div>
                        <div class="inline-flex items-center rounded-full border border-emerald-200 bg-emerald-100 px-3 py-1 text-sm font-semibold text-emerald-800 dark:border-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-200">
                            STATUS: <span class="font-semibold">{{ Str::upper($this->paymentDetails['transaction_status'] ?? 'pending') }}</span>
                        </div>
                    </div>

                    <div class="mt-6 rounded-2xl border border-emerald-200 bg-white p-5 dark:border-emerald-900/40 dark:bg-gray-900">
                        @if($this->selectedPaymentType === 'bank_transfer')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Bank</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">{{ strtoupper($this->selectedBank) }}</div>
                                </div>

                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Jumlah</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($this->order->total_idr) }}</div>
                                </div>
                            </div>

                            @if(isset($this->paymentDetails['va_numbers']))
                                @foreach($this->paymentDetails['va_numbers'] as $va)
                                    <div class="mt-4">
                                        <div class="text-xs text-gray-500 dark:text-gray-400">Nomor VA</div>
                                        <div class="mt-1 flex flex-wrap items-center gap-2">
                                            <div class="rounded-xl border border-gray-200 bg-gray-50 px-3 py-2 font-mono text-sm font-semibold text-gray-900 dark:border-gray-800 dark:bg-gray-950 dark:text-white">{{ $va['va_number'] }}</div>
                                            <button type="button" @click="copy('{{ $va['va_number'] }}'); $el.textContent = 'Tersalin!'; setTimeout(() => $el.textContent = 'Copy', 2000)" x-ref="copyBtn" class="rounded-xl bg-blue-600 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-700">Copy</button>
                                        </div>
                                    </div>
                                @endforeach
                            @endif

                            <div class="mt-4 text-xs text-gray-600 dark:text-gray-400">
                                Batas pembayaran: <span class="font-semibold">{{ optional($this->order->created_at)->addHours(24)->format('d M Y, H:i') }}</span>
                            </div>
                        @elseif($this->selectedPaymentType === 'gopay')
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Metode</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">QRIS</div>
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Jumlah</div>
                                    <div class="mt-1 text-sm font-semibold text-gray-900 dark:text-white">Rp {{ number_format($this->order->total_idr) }}</div>
                                </div>
                            </div>

                            @if(isset($this->paymentDetails['actions']))
                                @foreach($this->paymentDetails['actions'] as $action)
                                    @if(($action['name'] ?? null) === 'generate-qr-code')
                                        <div class="mt-5">
                                            <div class="text-xs text-gray-500 dark:text-gray-400">QR Code</div>
                                            <div class="mt-2 overflow-hidden rounded-2xl border border-gray-200 bg-white p-3 dark:border-gray-800 dark:bg-gray-950">
                                                <img src="{{ $action['url'] }}" alt="QR Code" class="mx-auto w-full max-w-xs rounded-xl" />
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            @endif

                            <div class="mt-4 text-xs text-gray-600 dark:text-gray-400">
                                Batas pembayaran: <span class="font-semibold">{{ optional($this->order->created_at)->addHours(24)->format('d M Y, H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            @else
                <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                    <div class="text-sm font-semibold text-gray-900 dark:text-white">Metode Pembayaran</div>

                    <div class="mt-4">
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">Transfer Bank</div>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                            @foreach($banks as $bankKey => $bankName)
                                <button
                                    type="button"
                                    wire:click="selectBankTransfer('{{ $bankKey }}')"
                                    class="group rounded-2xl border p-3 text-left transition-colors {{
                                        $this->selectedPaymentType === 'bank_transfer' && $this->selectedBank === $bankKey
                                            ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20'
                                            : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800'
                                    }}"
                                >
                                    <div class="text-sm font-bold text-gray-900 dark:text-white">{{ strtoupper($bankKey) }}</div>
                                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ $bankName }}</div>
                                </button>
                            @endforeach
                        </div>
                    </div>

                    <div class="mt-6">
                        <div class="text-sm font-semibold text-gray-800 dark:text-gray-200 mb-4">QRIS</div>
                        <button
                            type="button"
                            wire:click="selectGopay"
                            class="w-full rounded-2xl border p-4 text-left transition-colors {{
                                $this->selectedPaymentType === 'gopay'
                                    ? 'border-emerald-500 bg-emerald-50 dark:bg-emerald-900/20'
                                    : 'border-gray-200 bg-white hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:hover:bg-gray-800'
                            }}"
                        >
                            <div class="text-sm font-bold text-gray-900 dark:text-white">GoPay / QRIS</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400">Scan QR menggunakan e-wallet / mobile banking</div>
                        </button>
                    </div>

                    <div class="mt-6 flex flex-col sm:flex-row gap-2 sm:items-center sm:justify-between">
                        <div class="text-xs text-gray-500 dark:text-gray-400">Pilih metode pembayaran, lalu proses untuk mendapatkan kode bayar.</div>
                        <button
                            type="button"
                            wire:click="processPayment"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center justify-center rounded-xl bg-blue-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-700 disabled:opacity-50"
                        >
                            <span wire:loading.remove wire:target="processPayment">Proses Pembayaran</span>
                            <span wire:loading wire:target="processPayment">Memproses...</span>
                        </button>
                    </div>

                    @error('payment')
                        <div class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-700 dark:border-red-900/40 dark:bg-red-900/20 dark:text-red-300">{{ $message }}</div>
                    @endif
                    </div>
                @endif
            @endif
        </div>

        <!-- Right: Ringkasan Pesanan + Panduan -->
        <div class="space-y-4">
            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="text-sm font-semibold text-gray-900 dark:text-white">Ringkasan Pesanan</div>
                <div class="mt-4 space-y-3">
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Pekerja</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $this->order->worker->name ?? 'N/A' }}</div>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Total</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">Rp {{ number_format($this->order->total_idr) }}</div>
                    </div>
                    <div class="flex items-start justify-between gap-4">
                        <div class="text-sm text-gray-600 dark:text-gray-400">Tanggal order</div>
                        <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ optional($this->order->created_at)->format('d M Y, H:i') }}</div>
                    </div>
                    <div class="pt-3 border-t border-gray-100 dark:border-gray-800">
                        <div class="flex items-start justify-between gap-4">
                            <div class="text-sm text-gray-600 dark:text-gray-400">Invoice</div>
                            <div class="flex items-center gap-2">
                                <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $this->order->code }}</div>
                                <button type="button" @click="copy('{{ $this->order->code }}'); $el.textContent = 'Tersalin!'; setTimeout(() => $el.textContent = 'Copy', 2000)" x-ref="copyBtnInvoice" class="rounded-lg border border-gray-200 bg-white px-2 py-1 text-xs font-semibold text-gray-700 hover:bg-gray-50 dark:border-gray-800 dark:bg-gray-900 dark:text-gray-200 dark:hover:bg-gray-800">Copy</button>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 dark:text-gray-400">
                            Bayar sebelum <span class="font-semibold">{{ optional($this->order->created_at)->addHours(24)->format('d M Y, H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-800 dark:bg-gray-900">
                <div class="text-sm font-semibold text-gray-900 dark:text-white">Panduan Singkat</div>
                <div class="mt-4 space-y-3">
                    <details class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-800 dark:text-gray-200">Cara bayar Transfer VA</summary>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <div>1. Salin nomor VA.</div>
                            <div>2. Buka m-banking / ATM, pilih menu transfer / virtual account.</div>
                            <div>3. Masukkan nomor VA lalu konfirmasi pembayaran.</div>
                        </div>
                    </details>
                    <details class="rounded-xl border border-gray-100 bg-gray-50 p-4 dark:border-gray-800 dark:bg-gray-950">
                        <summary class="cursor-pointer text-sm font-semibold text-gray-800 dark:text-gray-200">Cara bayar QRIS</summary>
                        <div class="mt-2 text-sm text-gray-600 dark:text-gray-400 space-y-1">
                            <div>1. Buka aplikasi e-wallet / mobile banking.</div>
                            <div>2. Scan QR lalu masukkan nominal jika diminta.</div>
                            <div>3. Selesaikan pembayaran dan kembali ke halaman ini untuk refresh status.</div>
                        </div>
                    </details>
                </div>
            </div>
        </div>
    </div>

</div>

<div class="space-y-5">
    @if (session()->has('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800 dark:border-green-600 dark:bg-green-900/80 dark:text-green-100 shadow-sm">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-800 dark:border-red-600 dark:bg-red-900/80 dark:text-red-100 shadow-sm">
            {{ session('error') }}
        </div>
    @endif

    @if(empty($payment))
        <div class="rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-600 dark:border-slate-800 dark:bg-slate-900 dark:text-slate-300">
            Data payment tidak ditemukan.
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">ID Payment</p>
                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-slate-50">#{{ $payment['id'] }}</p>
                <p class="mt-2 text-xs text-gray-500 dark:text-slate-400">Dibuat {{ $payment['created_at'] }}</p>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</p>
                @php
                    $statusColors = [
                        'initiated' => 'bg-slate-100 text-slate-700 dark:bg-slate-700 dark:text-slate-200',
                        'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/25 dark:text-amber-200',
                        'settlement' => 'bg-green-100 text-green-800 dark:bg-green-500/25 dark:text-green-200',
                        'expire' => 'bg-gray-200 text-gray-700 dark:bg-slate-700 dark:text-slate-200',
                        'cancel' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/25 dark:text-rose-200',
                        'deny' => 'bg-red-100 text-red-800 dark:bg-red-500/25 dark:text-red-200',
                        'chargeback' => 'bg-violet-100 text-violet-800 dark:bg-violet-500/25 dark:text-violet-200',
                    ];
                @endphp
                <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$payment['status']] ?? 'bg-gray-100 text-gray-700 dark:bg-slate-700 dark:text-slate-200' }}">
                    {{ \App\Domain\Shared\Statuses\PaymentStatus::label($payment['status']) }}
                </span>
                <div class="mt-3 text-sm text-gray-700 dark:text-slate-200">
                    <p><span class="text-gray-500 dark:text-slate-400">Metode:</span> {{ $payment['payment_method'] ?: '-' }}</p>
                    <p class="mt-1"><span class="text-gray-500 dark:text-slate-400">Settled:</span> {{ $payment['settled_at'] ?: '-' }}</p>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70">
                <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Jumlah</p>
                <p class="mt-2 text-xl font-bold text-gray-900 dark:text-slate-50">Rp {{ number_format($payment['amount_idr'], 0, ',', '.') }}</p>
                <div class="mt-3 text-sm text-gray-700 dark:text-slate-200">
                    <p><span class="text-gray-500 dark:text-slate-400">Transaction ID:</span> <span class="font-mono">{{ $payment['transaction_id'] ?: '-' }}</span></p>
                    <p class="mt-1"><span class="text-gray-500 dark:text-slate-400">Midtrans Order ID:</span> <span class="font-mono">{{ $payment['midtrans_order_id'] }}</span></p>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Informasi Order</h2>
                <a href="{{ route('admin.orders.show', $payment['order_id']) }}" class="inline-flex items-center gap-1 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition-colors">
                    Detail Order
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </a>
            </div>
            <div class="mt-6 grid grid-cols-1 gap-6 md:grid-cols-3 text-sm">
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Kode Order</p>
                    <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $payment['order']['code'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Customer</p>
                    <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $payment['order']['visitor_name'] ?? '-' }}</p>
                    <p class="text-xs text-gray-500 dark:text-slate-400">{{ $payment['order']['visitor_email'] ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Agency</p>
                    <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $payment['order']['agency_name'] ?? '-' }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Request ke Midtrans</h2>
                    <span class="text-xs text-gray-500 dark:text-slate-400">Payload awal</span>
                </div>
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-900 p-4 dark:border-slate-700 dark:bg-gray-900">
                    <pre class="text-xs text-white font-mono leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($payment['request_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Callback Terakhir Midtrans</h2>
                    <span class="text-xs text-gray-500 dark:text-slate-400">Webhook terakhir</span>
                </div>
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-900 p-4 dark:border-slate-700 dark:bg-gray-900">
                    <pre class="text-xs text-white font-mono leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($payment['last_callback_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
            <div class="flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Log Webhook Midtrans (Payment Attempts)</h2>
                <span class="text-xs text-gray-500 dark:text-slate-400">{{ count($attempts) }} log</span>
            </div>

            @if(empty($attempts))
                <div class="mt-6 text-sm text-gray-500 dark:text-slate-400">Belum ada log webhook yang tersimpan.</div>
            @else
                <div class="mt-6 space-y-4">
                    @foreach($attempts as $a)
                        <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-slate-700 dark:bg-slate-800/60">
                            <div class="flex flex-col gap-2 md:flex-row md:items-center md:justify-between">
                                <div class="text-sm font-semibold text-gray-900 dark:text-slate-50">Attempt #{{ $a['id'] }} - {{ $a['status'] }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-400">{{ $a['created_at'] }}</div>
                            </div>
                            <div class="mt-2 grid grid-cols-1 gap-3 md:grid-cols-3 text-sm">
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Midtrans Order ID</p>
                                    <p class="font-mono text-gray-900 dark:text-slate-50">{{ $a['midtrans_order_id'] }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Transaction ID</p>
                                    <p class="font-mono text-gray-900 dark:text-slate-50">{{ $a['transaction_id'] ?: '-' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-slate-400">Processed</p>
                                    <p class="text-gray-900 dark:text-slate-50">{{ $a['processed_at'] ?: '-' }}</p>
                                </div>
                            </div>

                            @if(!empty($a['error_message']))
                                <div class="mt-3 rounded-lg border border-red-200 bg-red-50 p-3 text-sm text-red-800 dark:border-red-500/60 dark:bg-red-900/40 dark:text-red-100">
                                    Error: {{ $a['error_message'] }}
                                </div>
                            @endif

                            <div class="mt-4 rounded-lg border border-gray-200 bg-gray-900 p-4 dark:border-slate-700 dark:bg-gray-900">
                                <pre class="text-xs text-white font-mono leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($a['raw_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

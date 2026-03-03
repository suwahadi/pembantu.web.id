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
                        'initiated' => 'bg-slate-500 text-white',
                        'pending' => 'bg-white text-gray-900 border border-gray-200 dark:bg-white dark:text-gray-900 dark:border-gray-700',
                        'settlement' => 'bg-green-500 text-white',
                        'expire' => 'bg-gray-500 text-white',
                        'canceled' => 'bg-red-600 text-white',
                        'deny' => 'bg-red-500 text-white',
                        'chargeback' => 'bg-violet-600 text-white',
                    ];
                @endphp
                <span class="mt-2 inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$payment['status']] ?? 'bg-gray-600 text-white' }}">
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
                <a href="{{ route('admin.orders.show', $payment['order_id']) }}" class="inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-6 py-2 text-sm font-semibold text-white hover:bg-blue-700 dark:bg-blue-500 dark:hover:bg-blue-600 transition-colors">
                    Detail Order
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
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-100 p-4 dark:border-slate-600 dark:bg-slate-700">
                    <pre class="text-xs text-gray-900 dark:text-slate-50 font-mono leading-relaxed whitespace-pre-wrap break-words">{{ json_encode($payment['request_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-800 dark:bg-slate-900">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Callback Terakhir Midtrans</h2>
                    <span class="text-xs text-gray-500 dark:text-slate-400">Webhook terakhir</span>
                </div>
                <div class="mt-4 rounded-lg border border-gray-200 bg-gray-100 p-4 dark:border-slate-600 dark:bg-slate-700">
                    <div class="overflow-x-auto">
                        <pre class="text-xs text-gray-900 dark:text-slate-50 font-mono leading-relaxed whitespace-pre">{{ json_encode($payment['last_callback_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                    </div>
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
                        <div class="rounded-xl border border-gray-200 bg-white p-4 dark:border-slate-700 dark:bg-slate-800">
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

                            <div class="mt-4 rounded-lg border border-gray-200 bg-gray-100 p-4 dark:border-slate-600 dark:bg-slate-700">
                                <pre class="text-xs text-gray-900 dark:text-slate-50 font-mono leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($a['raw_payload'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

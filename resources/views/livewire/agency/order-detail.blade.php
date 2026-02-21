<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Order</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Informasi lengkap dan timeline pekerjaan</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('agency.orders.index') }}" class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-4 bg-success-50 dark:bg-success-900/20 border border-success-200 dark:border-success-800 text-success-700 dark:text-success-300 rounded-lg p-4">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-error-50 dark:bg-error-900/20 border border-error-200 dark:border-error-800 text-error-700 dark:text-error-300 rounded-lg p-4">
            {{ session('error') }}
        </div>
    @endif

    @if(!$order)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="text-sm text-gray-600 dark:text-gray-400">Order tidak ditemukan.</div>
        </div>
    @else
        <!-- Order Information Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6 mb-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Informasi Order</h3>
                <span class="px-3 py-1 text-xs font-medium rounded-full 
                    @if($order->status == 'paid_escrow') bg-brand-100 text-brand-800 dark:bg-brand-500/20 dark:text-brand-400
                    @elseif($order->status == 'in_progress') bg-success-100 text-success-800 dark:bg-success-500/20 dark:text-success-400
                    @elseif($order->status == 'completed') bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400
                    @else bg-warning-100 text-warning-800 dark:bg-warning-500/20 dark:text-warning-400 @endif">
                    {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Kode Order</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ $order->code }}</div>
                    </div>
                    
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Customer</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ $order->visitor_name ?? '-' }}</div>
                    </div>
                    
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Worker</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ $order->worker_name }}</div>
                    </div>
                    
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Skema</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ $order->scheme }}</div>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Total Harga</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</div>
                    </div>
                    
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Mulai</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ \Carbon\Carbon::parse($order->contract_start_date)->translatedFormat('d F Y') }}</div>
                    </div>
                    
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Selesai</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ \Carbon\Carbon::parse($order->contract_end_date)->translatedFormat('d F Y') }}</div>
                    </div>
                    
                    <div>
                        <span class="text-sm text-gray-500 dark:text-gray-400">Tanggal Order</span>
                        <div class="text-sm font-medium text-gray-800 dark:text-white/90 mt-1">{{ \Carbon\Carbon::parse($order->created_at)->translatedFormat('d F Y H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-700 flex flex-wrap gap-3">
                @if($order->status === 'paid_escrow')
                    <button type="button" wire:click="startJob" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="!$wire.loading" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span wire:loading.remove>Mulai Pekerjaan</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                @elseif($order->status === 'in_progress')
                    <button type="button" wire:click="finishJob" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-success-500 text-white rounded-lg hover:bg-success-600 transition-colors disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2">
                        <svg x-show="!$wire.loading" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span wire:loading.remove>Tandai Selesai</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Timeline Card -->
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Timeline</h3>
                <span class="text-sm text-gray-500 dark:text-gray-400">Riwayat event order</span>
            </div>
            
            @if(empty($events))
                <div class="text-center py-8">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada event.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($events as $event)
                        <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                            <div class="h-8 w-8 bg-brand-100 dark:bg-brand-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                                <svg class="h-4 w-4 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </div>
                            <div class="flex-1">
                                <div class="flex items-center justify-between">
                                    <p class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $event->event_type ?? 'Event' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($event->created_at)->format('d M Y H:i') }}</p>
                                </div>
                                @if($event->description)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $event->description }}</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>

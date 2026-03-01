<div>
<div class="space-y-6 text-gray-900 dark:text-slate-100" x-data>
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

    @if (empty($order))
        <div class="rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-600 dark:border-gray-800 dark:bg-white/[0.02] dark:text-gray-300">
            Data order tidak ditemukan.
        </div>
    @else
        <div class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="flex flex-col gap-y-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Kode Order</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-slate-50">{{ $order['code'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-300">Dibuat {{ $order['created_at'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="flex flex-col gap-y-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Status</p>
                        @php
                            $badgeColors = [
                                'pending_payment' => 'bg-amber-500',
                                'paid_escrow' => 'bg-sky-500',
                                'in_progress' => 'bg-blue-500',
                                'completed_by_agency' => 'bg-indigo-500',
                                'completed' => 'bg-green-600',
                                'disputed' => 'bg-red-500',
                                'canceled' => 'bg-red-600',
                                'cancelled' => 'bg-red-600',
                                'refunded' => 'bg-red-600',
                            ];
                        @endphp
                        <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold text-white shadow-sm {{ $badgeColors[$order['status']] ?? 'bg-gray-600' }}">
                            {{ $order['status_label'] }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-gray-500 dark:text-slate-300">Terakhir diperbarui {{ $order['updated_at'] }}</p>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="flex flex-col gap-y-2">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Total Pembayaran</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-slate-50">Rp {{ number_format($order['total_idr'], 0, ',', '.') }}</p>
                    </div>
                    <div class="mt-3 space-y-1 text-sm text-gray-600 dark:text-slate-200">
                        <p>Subtotal: Rp {{ number_format($order['subtotal_idr'], 0, ',', '.') }}</p>
                        <p>Platform Fee: Rp {{ number_format($order['platform_fee_idr'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Informasi Order</h3>
                    <span class="text-xs text-gray-500 dark:text-slate-300">ID #{{ $order['id'] }}</span>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Kategori</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['category']['name'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Durasi</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['duration'] }} hari</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Tanggal Mulai</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['start_date'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Tanggal Selesai</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['end_date'] }}</p>
                    </div>
                </div>
                @if($order['notes'])
                    <div class="mt-6">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Catatan</p>
                        <p class="mt-2 text-sm text-gray-700 dark:text-slate-200">{{ $order['notes'] }}</p>
                    </div>
                @endif
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Informasi Pihak Terkait</h3>
                    <span class="text-xs text-gray-500 dark:text-slate-300">Visitor & Agency</span>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Visitor</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['visitor']['name'] ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-300">{{ $order['visitor']['email'] ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-300">{{ $order['visitor']['phone'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Agency</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['agency']['company_name'] ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-300">{{ $order['agency']['email'] ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-300">{{ $order['agency']['phone'] ?? '-' }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Worker</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['worker']['name'] ?? '-' }}</p>
                        <p class="text-xs text-gray-500 dark:text-slate-300">{{ $order['worker']['phone'] ?? '-' }}</p>
                    </div>
                </div>
            </div>
        </div>

        @if($order['contract'])
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Kontrak</h3>
                    <span class="text-xs text-gray-500 dark:text-slate-300">Detail Kontrak</span>
                </div>
                <div class="mt-6 grid grid-cols-2 gap-6 text-sm">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Tanggal Mulai Kontrak</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['contract']['start_date'] }}</p>
                    </div>
                    <div>
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Tanggal Selesai Kontrak</p>
                        <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $order['contract']['end_date'] }}</p>
                    </div>
                </div>
                @if(!empty($order['contract']['metadata']))
                    <div class="mt-6">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-400">Metadata</p>
                        <div class="mt-2 rounded-lg border border-gray-200 bg-gray-900 p-4 dark:border-slate-600 dark:bg-gray-900 dark:shadow-inner">
                            <pre class="text-xs text-white dark:text-white font-mono leading-relaxed whitespace-pre-wrap break-all">{{ json_encode($order['contract']['metadata'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        </div>
                    </div>
                @endif
            </div>
        @endif

        <form wire:submit.prevent="updateOrder" class="space-y-8">
            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Ubah Data Order</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Lengkapi form berikut untuk memperbarui status dan detail order.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Status</label>
                        <select wire:model.defer="form.status" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50">
                            <option value="">Pilih status</option>
                            @foreach($statusLabels as $val => $label)
                                <option value="{{ $val }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('form.status')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Mata Uang</label>
                        <input type="text" maxlength="3" wire:model.defer="form.currency" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm uppercase text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                        @error('form.currency')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Tanggal Mulai</label>
                        <input type="date" wire:model.defer="form.start_date" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                        @error('form.start_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Tanggal Selesai</label>
                        <input type="date" wire:model.defer="form.end_date" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                        @error('form.end_date')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Informasi Pembayaran</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Atur subtotal, platform fee, dan total pembayaran.</p>
                </div>

                <div class="grid grid-cols-1 gap-6 md:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Subtotal (IDR)</label>
                        <input type="number" min="0" step="1000" wire:model.defer="form.subtotal_idr" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                        @error('form.subtotal_idr')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Platform Fee (IDR)</label>
                        <input type="number" min="0" step="1000" wire:model.defer="form.platform_fee_idr" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                        @error('form.platform_fee_idr')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Total (IDR)</label>
                        <input type="number" min="0" step="1000" wire:model.defer="form.total_idr" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                        @error('form.total_idr')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="mb-8">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Catatan</h2>
                    <p class="text-sm text-gray-500 dark:text-slate-400">Tambahkan catatan tambahan jika diperlukan (maks. 1000 karakter).</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Catatan Order</label>
                    <textarea rows="4" wire:model.defer="form.notes" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" placeholder="Opsional"></textarea>
                    @error('form.notes')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex items-center justify-between mt-8">
                <button type="button" wire:click="loadOrder" wire:loading.attr="disabled" class="px-4 py-2 rounded-lg border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200 dark:hover:bg-slate-700 transition-colors">
                    Muat Ulang
                </button>
                <button type="submit" wire:loading.attr="disabled" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white shadow-lg transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-brand-700 dark:hover:bg-brand-800">
                    <svg wire:loading class="h-4 w-4 animate-spin" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4A8 8 0 104 12z"></path>
                    </svg>
                    <span wire:loading.remove>Simpan Perubahan</span>
                    <span wire:loading>Memproses...</span>
                </button>
            </div>
        </form>

        <div class="grid gap-6 lg:grid-cols-2">
            <div class="rounded-2xl border border-rose-200 bg-rose-50 p-6 dark:border-rose-500/40 dark:bg-rose-500/10 md:p-8">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-rose-900 dark:text-rose-100">Hapus Order</h3>
                        <p class="mt-1 text-sm text-rose-700/80 dark:text-rose-200/80">Tindakan ini permanen dan tidak dapat dibatalkan.</p>
                    </div>
                    <button wire:click="confirmDelete" type="button" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                        Hapus Order
                    </button>
                </div>
            </div>

            @if($order['events'] && count($order['events']) > 0)
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Event Timeline</h3>
                        <span class="text-xs text-gray-500 dark:text-slate-300">{{ count($order['events']) }} event</span>
                    </div>
                    <div class="mt-6 space-y-4">
                        @foreach($order['events'] as $event)
                            <div class="flex items-start gap-4">
                                <div class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full bg-gray-100 dark:bg-slate-700">
                                    <svg class="h-4 w-4 text-gray-600 dark:text-slate-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-medium text-gray-900 dark:text-slate-50">{{ $event['description'] }}</p>
                                    <p class="text-xs text-gray-500 dark:text-slate-300">{{ $event['created_at'] }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>

<!-- Delete Confirmation Dialog -->
<div x-show="$wire.get('showDeleteConfirm')" 
     x-transition:enter="transition ease-out duration-300"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-50 overflow-y-auto"
     style="display: none;">
    <div class="flex min-h-screen items-center justify-center p-4">
        <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75" x-show="$wire.get('showDeleteConfirm')"></div>
        
        <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-slate-800 dark:shadow-2xl">
            <div class="text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-red-100 dark:bg-red-500/20">
                    <svg class="h-6 w-6 text-red-600 dark:text-red-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Hapus Order</h3>
                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    Apakah Anda yakin ingin menghapus order ini? Tindakan ini tidak dapat dibatalkan.
                </p>
            </div>
            
            <div class="mt-6 flex gap-3">
                <button type="button" 
                        @click="$wire.set('showDeleteConfirm', false)"
                        class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600">
                    Tidak
                </button>
                <button type="button" 
                        wire:click="deleteOrder"
                        @click="$wire.set('showDeleteConfirm', false)"
                        class="flex-1 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                    Ya, Hapus
                </button>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:init', () => {
        Livewire.on('show-delete-confirm', () => {
            @this.set('showDeleteConfirm', true);
        });
    });
</script>
</div>

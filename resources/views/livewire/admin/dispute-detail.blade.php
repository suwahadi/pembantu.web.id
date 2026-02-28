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

        @if (empty($dispute))
            <div class="rounded-2xl border border-gray-200 bg-white p-6 text-sm text-gray-600 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-300">
                Data dispute tidak ditemukan.
            </div>
        @else
            <div class="grid gap-6 lg:grid-cols-3">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">ID Dispute</p>
                    <p class="mt-1 text-lg font-semibold text-gray-900 dark:text-slate-50">#{{ $dispute['id'] }}</p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-slate-300">Dibuat {{ $dispute['created_at'] }}</p>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Status</p>
                    @php
                        $statusColors = [
                            'open' => 'bg-amber-100 text-amber-800 dark:bg-amber-500/20 dark:text-amber-200',
                            'investigating' => 'bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-200',
                            'resolved' => 'bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-200',
                            'rejected' => 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-200',
                        ];
                    @endphp
                    <span class="mt-2 inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold {{ $statusColors[$dispute['status']] ?? 'bg-gray-100 text-gray-700 dark:bg-gray-500/20 dark:text-gray-200' }}">
                        {{ $dispute['status_label'] }}
                    </span>
                    @if($dispute['resolved_at'])
                        <p class="mt-3 text-xs text-gray-500 dark:text-slate-300">Diselesaikan {{ $dispute['resolved_at'] }}</p>
                    @endif
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                    <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Order</p>
                    <p class="mt-1 text-sm font-semibold text-gray-900 dark:text-slate-50">{{ $dispute['order']['code'] ?? ('#' . $dispute['order_id']) }}</p>
                    <p class="mt-2 text-xs text-gray-500 dark:text-slate-300">Total: Rp {{ number_format($dispute['order']['total_idr'] ?? 0, 0, ',', '.') }}</p>
                    <div class="mt-3">
                        <a href="{{ route('admin.orders.show', $dispute['order_id']) }}" class="inline-flex items-center gap-1 rounded-lg bg-gray-900 px-3 py-2 text-xs font-semibold text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition-colors">
                            Detail Order
                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                            </svg>
                        </a>
                    </div>
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Keluhan</h3>
                        <span class="text-xs text-gray-500 dark:text-slate-300">Oleh: {{ $dispute['opened_by']['name'] ?? '-' }}</span>
                    </div>
                    <div class="mt-4 rounded-lg bg-gray-50 p-4 text-sm text-gray-700 dark:bg-slate-800/60 dark:text-slate-200">
                        {{ $dispute['complaint'] }}
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                    <div class="flex items-center justify-between">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Pihak Terkait</h3>
                        <span class="text-xs text-gray-500 dark:text-slate-300">Order #{{ $dispute['order_id'] }}</span>
                    </div>
                    <div class="mt-6 grid grid-cols-1 gap-4 text-sm">
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Customer</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $dispute['order']['visitor_name'] ?? '-' }}</p>
                        </div>
                        <div>
                            <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Agency</p>
                            <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $dispute['order']['agency_name'] ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Bukti (Evidence)</h3>
                    <span class="text-xs text-gray-500 dark:text-slate-300">{{ count($dispute['evidences']) }} file</span>
                </div>

                @if(empty($dispute['evidences']))
                    <div class="mt-6 text-sm text-gray-500 dark:text-slate-300">Belum ada bukti yang diunggah.</div>
                @else
                    <div class="mt-6 grid gap-4 md:grid-cols-2">
                        @foreach($dispute['evidences'] as $e)
                            <div class="rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-slate-600 dark:bg-slate-800/60">
                                <p class="text-sm font-semibold text-gray-900 dark:text-slate-50">{{ $e['description'] ?: 'Bukti Dispute' }}</p>
                                <p class="mt-1 text-xs text-gray-500 dark:text-slate-300">Diunggah {{ $e['created_at'] }}</p>
                                <a href="{{ asset('storage/' . $e['file_path']) }}" target="_blank" class="mt-3 inline-flex items-center gap-1 text-xs font-semibold text-blue-700 hover:underline dark:text-blue-300">
                                    Lihat File
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <div class="space-y-8">
                <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                    <div class="mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Keputusan Admin</h2>
                        <p class="text-sm text-gray-500 dark:text-slate-400">Isi catatan dan pilih aksi penyelesaian. Pastikan keputusan sesuai bukti.</p>
                    </div>

                    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Catatan Resolusi</label>
                            <textarea rows="4" wire:model.defer="form.resolution_note" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" placeholder="Tuliskan alasan keputusan... (wajib jika ditolak)"></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Refund (IDR)</label>
                            <input type="number" min="0" step="1000" wire:model.defer="form.refund_amount_idr" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" placeholder="0" />
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Release ke Agency (IDR)</label>
                            <input type="number" min="0" step="1000" wire:model.defer="form.release_amount_idr" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" placeholder="0" />
                        </div>
                    </div>

                    <div class="mt-6 flex flex-wrap gap-3">
                        <button type="button" wire:click="confirmAction('full_refund')" class="rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500">
                            Refund Penuh
                        </button>
                        <button type="button" wire:click="confirmAction('full_release')" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            Release Penuh
                        </button>
                        <button type="button" wire:click="confirmAction('partial')" class="rounded-lg bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            Split (Partial)
                        </button>
                        <button type="button" wire:click="confirmAction('reject')" class="rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                            Tolak Dispute
                        </button>
                    </div>
                </div>

                @if(!empty($dispute['resolution_note']) || !empty($dispute['decision']))
                    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Ringkasan Keputusan</h3>
                            <span class="text-xs text-gray-500 dark:text-slate-300">{{ $dispute['status_label'] }}</span>
                        </div>
                        <div class="mt-4 grid gap-4 md:grid-cols-3 text-sm">
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Decision</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">{{ $dispute['decision'] ?? '-' }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Refund</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">Rp {{ number_format($dispute['refund_amount_idr'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                            <div>
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Release</p>
                                <p class="mt-1 font-semibold text-gray-900 dark:text-slate-50">Rp {{ number_format($dispute['release_amount_idr'] ?? 0, 0, ',', '.') }}</p>
                            </div>
                        </div>
                        @if(!empty($dispute['resolution_note']))
                            <div class="mt-6">
                                <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-slate-300">Catatan</p>
                                <div class="mt-2 rounded-lg bg-gray-50 p-4 text-sm text-gray-700 dark:bg-slate-800/60 dark:text-slate-200">
                                    {{ $dispute['resolution_note'] }}
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Confirm Dialog -->
    <div x-show="$wire.get('showResolveConfirm')"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-gray-500/75 dark:bg-gray-900/75" x-show="$wire.get('showResolveConfirm')"></div>

            <div class="relative w-full max-w-md transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all dark:bg-slate-800 dark:shadow-2xl">
                <div class="text-center">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-amber-100 dark:bg-amber-500/20">
                        <svg class="h-6 w-6 text-amber-600 dark:text-amber-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="mt-4 text-lg font-semibold text-gray-900 dark:text-white">Konfirmasi Aksi</h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        Lanjutkan aksi ini? Pastikan data sudah benar.
                    </p>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="button"
                            @click="$wire.set('showResolveConfirm', false)"
                            class="flex-1 rounded-lg border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 transition hover:bg-gray-50 dark:border-gray-600 dark:bg-slate-700 dark:text-gray-300 dark:hover:bg-slate-600">
                        Tidak
                    </button>
                    <button type="button"
                            wire:click="runPendingAction"
                            @click="$wire.set('showResolveConfirm', false)"
                            class="flex-1 rounded-lg bg-gray-900 px-4 py-2 text-sm font-medium text-white transition hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-gray-500 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100">
                        Ya
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

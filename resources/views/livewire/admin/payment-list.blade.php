<div class="space-y-5">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">History Payment</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400">Data pembayaran dari Midtrans untuk kebutuhan monitoring dan audit.</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="clearFilters" type="button" class="px-4 py-2 text-sm border border-gray-200 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">Reset Filter</button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Cari Payment</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Order code, midtrans order id, atau transaction id" class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-white dark:bg-slate-800 dark:border-slate-600 text-gray-900 dark:text-slate-50 placeholder:text-gray-400 dark:placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Status</label>
                <select wire:model.live="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-white dark:bg-slate-800 dark:border-slate-600 text-gray-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    @foreach($statusLabels as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-[980px] w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-800/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Order</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Midtrans Order ID</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Metode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Jumlah</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Dibuat</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                    @forelse($payments as $p)
                        @php
                            $statusColors = [
                                'initiated' => 'bg-slate-500 text-white',
                                'pending' => 'bg-white text-gray-900 border border-gray-200 dark:bg-white dark:text-gray-900 dark:border-gray-700',
                                'settlement' => 'bg-green-500 text-white',
                                'expire' => 'bg-gray-500 text-white',
                                'cancel' => 'bg-rose-500 text-white',
                                'deny' => 'bg-red-600 text-white',
                                'chargeback' => 'bg-violet-600 text-white',
                            ];
                        @endphp
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/80">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-900 dark:text-slate-50">{{ optional($p->order)->code ?? ('Order #' . $p->order_id) }}</div>
                                <div class="text-xs text-gray-500 dark:text-slate-400">ID Payment #{{ $p->id }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-50 font-mono">{{ $p->midtrans_order_id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700 dark:text-slate-200">{{ $p->payment_method ?: '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $statusColors[$p->status] ?? 'bg-gray-600 text-white' }}">
                                    {{ \App\Domain\Shared\Statuses\PaymentStatus::label($p->status) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-50">Rp {{ number_format((int)$p->amount_idr, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ $p->created_at?->format('d M Y H:i') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('admin.payment.show', $p->id) }}" class="inline-flex items-center gap-1 rounded-full border border-primary-200 px-3 py-1 text-xs font-semibold bg-gray-900 text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:hover:bg-gray-100 transition-colors">
                                    Detail
                                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-10 text-center text-sm text-gray-500 dark:text-slate-400">Belum ada data payment.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-5">{{ $payments->links() }}</div>
    </div>
</div>

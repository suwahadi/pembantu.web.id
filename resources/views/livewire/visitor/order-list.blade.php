<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Pesanan Saya</h1>
            <p class="text-gray-600 dark:text-gray-400">Kelola dan pantau status pesanan jasa Anda</p>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            @if($orders->isEmpty())
                <div class="p-12 text-center">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 dark:bg-gray-800 mb-4">
                        <x-icon.logo class="w-8 h-8 text-gray-400" />
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Belum ada pesanan</h3>
                    <p class="text-gray-500 dark:text-gray-400 mt-1">Anda belum melakukan pemesanan jasa apapun.</p>
                    <a href="{{ route('search') }}" class="mt-6 inline-flex items-center px-6 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-blue-600 hover:bg-blue-700 transition">
                        Cari Jasa Sekarang
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-800/50">
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">ID Pesanan</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Pekerja</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                                <th class="px-6 py-4 text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                            @foreach($orders as $order)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors">
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">#{{ $order->code }}</span>
                                        <div class="text-xs text-gray-500 mt-0.5">{{ $order->created_at->format('d M Y') }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $order->worker->name ?? 'Pekerja' }}</div>
                                        <div class="text-xs text-gray-500">{{ $order->worker->category->name ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @php
                                            $statusColors = [
                                                'pending_payment' => 'bg-white text-gray-900 border border-gray-200 dark:bg-white dark:text-gray-900 dark:border-gray-700',
                                                'paid_escrow' => 'bg-blue-500 text-white',
                                                'in_progress' => 'bg-blue-500 text-white',
                                                'completed_by_agency' => 'bg-indigo-500 text-white',
                                                'completed' => 'bg-green-600 text-white',
                                                'disputed' => 'text-white',
                                                'canceled' => 'bg-red-600 text-white',
                                                'cancelled' => 'bg-red-600 text-white',
                                                'refunded' => 'bg-red-600 text-white',
                                            ];
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $statusColors[$order->status] ?? 'bg-gray-600 text-white' }}" @if($order->status === 'disputed') style="background-color: #D4AF37" @endif>
                                            {{ ucfirst(str_replace('_', ' ', $order->status)) }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm font-bold text-gray-900 dark:text-white">
                                        Rp {{ number_format($order->total_idr, 0, ',', '.') }}
                                    </td>
                                    <td class="px-6 py-4">
                                        <a href="{{ route('orders.show', $order) }}" class="text-blue-600 dark:text-blue-400 hover:underline text-sm font-semibold">Detail</a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50">
                    {{ $orders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

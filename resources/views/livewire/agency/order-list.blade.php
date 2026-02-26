<div class="space-y-4">
    <!-- Search and Filter Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Order Management</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400">Kelola order yang sedang berjalan</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari Kode / Worker / Visitor</label>
                <input type="text" 
                    wire:model.live="q" 
                    placeholder="Cari order..." 
                    class="w-full h-10 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
            </div>
            
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status</label>
                <select wire:model.live="status" 
                    class="w-full h-10 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-700 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    <option value="">- Semua Status -</option>
                    @foreach($statuses as $status)
                        <option value="{{ $status }}">{{ $status }}</option>
                    @endforeach
                </select>
            </div>
            
        </div>
    </div>

    <!-- Orders Table -->
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Worker</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Visitor</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Periode</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"></th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $o)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $o->code }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $o->worker_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $o->visitor_name ?? '-' }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">
                                {{ \Carbon\Carbon::parse($o->contract_start_date)->format('d/m') }} -
                                {{ \Carbon\Carbon::parse($o->contract_end_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Rp {{ number_format($o->total_idr, 0, ',', '.') }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @if($o->status == 'paid_escrow') bg-brand-100 text-brand-800 dark:bg-brand-500/20 dark:text-brand-400
                                    @elseif($o->status == 'in_progress') bg-success-100 text-success-800 dark:bg-success-500/20 dark:text-success-400
                                    @elseif($o->status == 'completed') bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400
                                    @else bg-warning-100 text-warning-800 dark:bg-warning-500/20 dark:text-warning-400 @endif">
                                    {{ ucfirst(str_replace('_', ' ', $o->status)) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <a href="{{ route('agency.orders.show', $o->id) }}" class="text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300">Detail</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                                Tidak ada order
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        @endif
    </div>
</div>

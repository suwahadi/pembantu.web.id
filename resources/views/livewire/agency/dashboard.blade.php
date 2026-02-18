<div class="py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Dashboard Agensi</h1>
            <p class="text-gray-600 dark:text-gray-400">Ringkasan performa bisnis Anda hari ini</p>
        </div>

        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Pekerja</div>
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stats['total_workers'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Pesanan Aktif</div>
                <div class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $stats['active_orders'] }}</div>
            </div>
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                <div class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Total Pendapatan</div>
                <div class="text-2xl font-bold text-green-600 dark:text-green-400">Rp {{ number_format($stats['total_earnings'], 0, ',', '.') }}</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white dark:bg-gray-900 p-6 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Aksi Cepat</h3>
                <div class="space-y-3">
                    <a href="{{ route('agency.workers.create') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                        <span class="font-medium">Tambah Pekerja Baru</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    <a href="{{ route('agency.orders.index') }}" class="flex items-center justify-between p-4 rounded-xl border border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-gray-800/30 transition">
                        <span class="font-medium">Lihat Semua Pesanan</span>
                        <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

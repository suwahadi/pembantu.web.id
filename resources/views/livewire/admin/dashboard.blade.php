<div>
<!-- Page Header -->
<div class="mb-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Dashboard Admin</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-1">Selamat datang di admin panel Pembantu.web.id</p>
        </div>
        <div class="flex items-center gap-3">
            <button class="px-4 py-2 bg-brand-500 text-white rounded-lg hover:bg-brand-600 transition-colors flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Tambah Agency
            </button>
        </div>
    </div>
</div>

<!-- Stats Grid -->
<div class="grid grid-cols-12 gap-4 md:gap-6 mb-6">
    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Agency</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-2">{{ $stats['total_agencies'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span class="text-success-600 dark:text-success-500">+12%</span> dari bulan lalu
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-brand-100 dark:bg-brand-500/20 rounded-xl">
                    <svg class="h-6 w-6 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Users</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-2">{{ $stats['total_users'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span class="text-success-600 dark:text-success-500">+8%</span> dari bulan lalu
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-blue-100 dark:bg-blue-500/20 rounded-xl">
                    <svg class="h-6 w-6 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Workers</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-2">{{ $stats['total_workers'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span class="text-success-600 dark:text-success-500">+15%</span> dari bulan lalu
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-purple-100 dark:bg-purple-500/20 rounded-xl">
                    <svg class="h-6 w-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 md:col-span-6 lg:col-span-3">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-800 dark:text-white/90 mt-2">{{ $stats['total_orders'] ?? 0 }}</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        <span class="text-success-600 dark:text-success-500">+23%</span> dari bulan lalu
                    </p>
                </div>
                <div class="flex items-center justify-center w-12 h-12 bg-orange-100 dark:bg-orange-500/20 rounded-xl">
                    <svg class="h-6 w-6 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="grid grid-cols-12 gap-4 md:gap-6 mb-6">
    <div class="col-span-12 lg:col-span-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Revenue Overview</h3>
                <select class="text-sm border border-gray-200 rounded-lg px-3 py-1.5 bg-white dark:bg-gray-800 dark:border-gray-700 text-gray-900 dark:text-white">
                    <option>7 Hari Terakhir</option>
                    <option>30 Hari Terakhir</option>
                    <option>3 Bulan Terakhir</option>
                </select>
            </div>
            <div class="h-64 flex items-center justify-center bg-gray-50 dark:bg-gray-800 rounded-xl">
                <div class="text-center">
                    <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                    <p class="text-gray-500 dark:text-gray-400">Chart akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Order Status</h3>
            </div>
            <div class="space-y-4">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-success-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Selesai</span>
                    </div>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $stats['completed_orders'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-brand-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Proses</span>
                    </div>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $stats['processing_orders'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-warning-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Pending</span>
                    </div>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $stats['pending_orders'] ?? 0 }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-3 h-3 bg-error-500 rounded-full"></div>
                        <span class="text-sm text-gray-600 dark:text-gray-400">Dibatalkan</span>
                    </div>
                    <span class="text-sm font-medium text-gray-800 dark:text-white/90">{{ $stats['cancelled_orders'] ?? 0 }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity & Quick Actions -->
<div class="grid grid-cols-12 gap-4 md:gap-6 mb-6">
    <div class="col-span-12 lg:col-span-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Aktivitas Terkini</h3>
                <a href="#" class="text-sm text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300">Lihat Semua</a>
            </div>
            <div class="space-y-4">
                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="h-8 w-8 bg-success-100 dark:bg-success-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-success-600 dark:text-success-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Agency baru terdaftar</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">PT. Maju Bersama - 2 jam yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="h-8 w-8 bg-brand-100 dark:bg-brand-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Pekerja baru ditambahkan</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Siti Aminah - 3 jam yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="h-8 w-8 bg-orange-100 dark:bg-orange-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Pesanan baru diterima</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">ORD-20240221-001 - 5 jam yang lalu</p>
                    </div>
                </div>

                <div class="flex items-start gap-3 p-3 rounded-lg bg-gray-50 dark:bg-gray-800">
                    <div class="h-8 w-8 bg-purple-100 dark:bg-purple-500/20 rounded-full flex items-center justify-center flex-shrink-0">
                        <svg class="h-4 w-4 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-gray-800 dark:text-white/90">Payout diproses</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Rp 5,000,000 - 6 jam yang lalu</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-span-12 lg:col-span-4">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Aksi Cepat</h3>
            <div class="space-y-3">
                <a href="{{ route('admin.agencies.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="h-10 w-10 bg-brand-100 dark:bg-brand-500/20 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-brand-600 dark:text-brand-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white/90">Tambah Agency</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Daftarkan agency baru</p>
                    </div>
                </a>

                <a href="{{ route('admin.users.create') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="h-10 w-10 bg-blue-100 dark:bg-blue-500/20 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 01-12 0v1a6 6 0 0012 0v-1z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white/90">Tambah User</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Buat akun pengguna</p>
                    </div>
                </a>

                <a href="{{ route('admin.payout-queue') }}" class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    <div class="h-10 w-10 bg-orange-100 dark:bg-orange-500/20 rounded-lg flex items-center justify-center">
                        <svg class="h-5 w-5 text-orange-600 dark:text-orange-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-medium text-gray-800 dark:text-white/90">Payout Queue</p>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Proses pembayaran</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders Table -->
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Pesanan Terbaru</h3>
        <a href="{{ route('admin.orders.index') }}" class="text-sm text-brand-600 dark:text-brand-400 hover:text-brand-700 dark:hover:text-brand-300">Lihat Semua</a>
    </div>
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead>
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kode</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Agency</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($latest_orders ?? [] as $order)
                <tr class="hover:bg-gray-50 dark:hover:bg-gray-800">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $order->code }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $order->customer_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $order->agency_name }}</td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($order->status == 'completed') bg-success-100 text-success-800 dark:bg-success-500/20 dark:text-success-400
                            @elseif($order->status == 'in_progress') bg-brand-100 text-brand-800 dark:bg-brand-500/20 dark:text-brand-400
                            @elseif($order->status == 'pending') bg-warning-100 text-warning-800 dark:bg-warning-500/20 dark:text-warning-400
                            @else bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400 @endif">
                            {{ $order->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y') }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                        Belum ada pesanan
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
</div>

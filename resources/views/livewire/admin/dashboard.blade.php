<div class="space-y-6">
  <!-- Stats Cards -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Dispute Aktif</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $kpi['dispute_open'] }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Open + Investigating</p>
        </div>
        <div class="h-12 w-12 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
          <x-icon.alert-circle class="h-6 w-6 text-orange-600 dark:text-orange-400" />
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Refund Antrian</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $kpi['refund_queued'] }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Queued + Processing</p>
        </div>
        <div class="h-12 w-12 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
          <x-icon.refresh-cw class="h-6 w-6 text-blue-600 dark:text-blue-400" />
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Payout Antrian</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $kpi['payout_queued'] }}</p>
          <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Queued + Processing</p>
        </div>
        <div class="h-12 w-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
          <x-icon.dollar-sign class="h-6 w-6 text-green-600 dark:text-green-400" />
        </div>
      </div>
    </div>
  </div>

  <!-- Order Stats -->
  <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Order Paid (Escrow)</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $kpi['order_paid_escrow'] ?? 0 }}</p>
        </div>
        <div class="h-12 w-12 bg-purple-100 dark:bg-purple-900/20 rounded-lg flex items-center justify-center">
          <x-icon.credit-card class="h-6 w-6 text-purple-600 dark:text-purple-400" />
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Order Berjalan</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $kpi['order_in_progress'] ?? 0 }}</p>
        </div>
        <div class="h-12 w-12 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg flex items-center justify-center">
          <x-icon.clock class="h-6 w-6 text-yellow-600 dark:text-yellow-400" />
        </div>
      </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <div class="flex items-center justify-between">
        <div>
          <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Order Selesai</p>
          <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ $kpi['order_completed'] ?? 0 }}</p>
        </div>
        <div class="h-12 w-12 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
          <x-icon.check-circle class="h-6 w-6 text-green-600 dark:text-green-400" />
        </div>
      </div>
    </div>
  </div>

  <!-- Quick Actions -->
  <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Quick Actions</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <a href="{{ route('admin.disputes') }}" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <div class="h-10 w-10 bg-orange-100 dark:bg-orange-900/20 rounded-lg flex items-center justify-center">
          <x-icon.alert-circle class="h-5 w-5 text-orange-600 dark:text-orange-400" />
        </div>
        <div>
          <p class="font-medium text-gray-900 dark:text-white">Kelola Dispute</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">Review dan proses dispute</p>
        </div>
      </a>

      <a href="{{ route('admin.refunds') }}" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <div class="h-10 w-10 bg-blue-100 dark:bg-blue-900/20 rounded-lg flex items-center justify-center">
          <x-icon.refresh-cw class="h-5 w-5 text-blue-600 dark:text-blue-400" />
        </div>
        <div>
          <p class="font-medium text-gray-900 dark:text-white">Kelola Refund</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">Proses permintaan refund</p>
        </div>
      </a>

      <a href="{{ route('admin.payouts') }}" class="flex items-center gap-3 p-4 rounded-lg border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
        <div class="h-10 w-10 bg-green-100 dark:bg-green-900/20 rounded-lg flex items-center justify-center">
          <x-icon.dollar-sign class="h-5 w-5 text-green-600 dark:text-green-400" />
        </div>
        <div>
          <p class="font-medium text-gray-900 dark:text-white">Kelola Payout</p>
          <p class="text-sm text-gray-500 dark:text-gray-400">Proses pembayaran agency</p>
        </div>
      </a>
    </div>
  </div>

  <!-- Recent Orders -->
  <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Order Terbaru</h2>
    <div class="overflow-auto border border-gray-200 dark:border-gray-700 rounded-lg">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-700">
          <tr>
            <th class="text-left p-3 font-medium text-gray-900 dark:text-white">Kode</th>
            <th class="text-left p-3 font-medium text-gray-900 dark:text-white">Status</th>
            <th class="text-left p-3 font-medium text-gray-900 dark:text-white">Total</th>
            <th class="text-left p-3 font-medium text-gray-900 dark:text-white">Tanggal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($latest ?? [] as $o)
            <tr class="border-t border-gray-200 dark:border-gray-700">
              <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $o->code }}</td>
              <td class="p-3">
                <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900/20 dark:text-blue-400">
                  {{ $o->status }}
                </span>
              </td>
              <td class="p-3 text-gray-900 dark:text-white">Rp {{ number_format($o->total_idr, 0, ',', '.') }}</td>
              <td class="p-3 text-gray-600 dark:text-gray-400">{{ \Carbon\Carbon::parse($o->created_at)->translatedFormat('d M Y') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
</div>

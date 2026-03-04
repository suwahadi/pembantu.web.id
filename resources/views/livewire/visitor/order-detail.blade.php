<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
  @if(session('success'))
    <div id="order-success-alert" class="mb-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl p-4 text-sm">
      {{ session('success') }}
    </div>
    <script>
      document.addEventListener('DOMContentLoaded', function () {
        const successAlert = document.getElementById('order-success-alert');
        if (successAlert) {
          successAlert.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    </script>
  @endif

  @if(!$order)
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="text-center py-8">
        <div class="text-sm text-gray-600 dark:text-gray-400">Order tidak ditemukan.</div>
        <a href="{{ route('orders.list') }}" class="mt-4 inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
          Kembali ke Daftar Order
        </a>
      </div>
    </div>
  @else
    <!-- Order Details Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Detail Pesanan</h1>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Informasi order dan status terkini.</p>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Left Column -->
        <div class="space-y-4">
          <div class="flex items-start justify-between">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Kode Order</div>
            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $order->code }}</div>
          </div>
          
          <div class="flex items-start justify-between">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</div>
            <div class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold
              {{ match($order->status) {
                'pending_payment' => 'border-yellow-200 bg-yellow-100 text-yellow-800 dark:border-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-200',
                'in_progress' => 'border-blue-200 bg-blue-100 text-blue-800 dark:border-blue-800 dark:bg-blue-900/30 dark:text-blue-200',
                'completed' => 'border-green-200 bg-green-100 text-green-800 dark:border-green-800 dark:bg-green-900/30 dark:text-green-200',
                'cancelled' => 'border-red-200 bg-red-100 text-red-800 dark:border-red-800 dark:bg-red-900/30 dark:text-red-200',
                default => 'border-gray-200 bg-gray-100 text-gray-800 dark:border-gray-800 dark:bg-gray-900/30 dark:text-gray-200'
              } }}">
              {{ Str::title(str_replace('_', ' ', $order->status)) }}
            </div>
          </div>

          <div class="flex items-start justify-between">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Agency</div>
            <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $order->agency_name ?? '-' }}</div>
          </div>

          <div class="flex items-start justify-between">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Worker</div>
            <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $order->worker_name ?? '-' }}</div>
          </div>
        </div>

        <!-- Right Column -->
        <div class="space-y-4">
          <div class="flex items-start justify-between">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Total</div>
            <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</div>
          </div>

          <div class="flex items-start justify-between">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Skema</div>
            <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">{{ $order->contract_scheme ?? '-' }}</div>
          </div>

          <div class="flex items-start justify-between">
            <div class="text-sm font-medium text-gray-600 dark:text-gray-400">Periode</div>
            <div class="text-sm font-semibold text-gray-900 dark:text-white text-right">
              {{ $order->contract_start_date ?? '-' }} s/d {{ $order->contract_end_date ?? '-' }}
            </div>
          </div>
        </div>
      </div>

      <!-- Action Buttons -->
      <div class="mt-6 pt-6 border-t border-gray-200 dark:border-gray-800">
        <div class="flex flex-wrap gap-3">
          <a href="{{ route('orders.list') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 text-gray-700 text-sm rounded-lg hover:bg-gray-50 transition-colors dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-800">
            Kembali ke Daftar Order
          </a>

          @if($order->status === 'pending_payment')
            <a href="/pembayaran/{{ $order->id }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
              Bayar Sekarang
            </a>
          @endif

          @if($order->status === 'in_progress')
            <button type="button" wire:click="markCompleted" wire:loading.attr="disabled" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 disabled:opacity-50 transition-colors">
              <span wire:loading.remove>Konfirmasi Selesai</span>
              <span wire:loading>Memproses...</span>
            </button>
          @endif
        </div>
      </div>
    </div>

    <!-- Timeline Card -->
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Timeline</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Riwayat event order (maks 50 terbaru).</p>
      </div>
      
      @if(empty($events))
        <div class="text-center py-8">
          <div class="text-sm text-gray-600 dark:text-gray-400">Belum ada event.</div>
        </div>
      @else
        <x-order.timeline :events="$events" />
      @endif
    </div>

    <!-- Dispute Form Card -->
    <div class="mt-6 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-800 dark:bg-gray-900">
      <div class="mb-4">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Pengajuan Dispute</h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Ajukan dispute jika terjadi kendala layanan pada pesanan ini.</p>
      </div>

      @if(in_array($order->status, ['in_progress', 'paid_escrow', 'completed'], true))
        <livewire:visitor.dispute-form :order-id="$order->id" :embedded="true" :key="'order-dispute-'.$order->id" />
      @elseif($order->status === 'disputed')
        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 dark:border-amber-800 dark:bg-amber-900/30 dark:text-amber-200">
          Dispute untuk pesanan ini sudah dibuka dan sedang ditinjau tim kami.
        </div>
      @else
        <div class="rounded-xl border border-gray-200 bg-gray-50 px-4 py-3 text-sm text-gray-700 dark:border-gray-700 dark:bg-gray-800/50 dark:text-gray-300">
          Dispute hanya bisa diajukan ketika pesanan berstatus <strong>Paid Escrow</strong>, <strong>In Progress</strong>, atau <strong>Completed</strong>.
        </div>
      @endif
    </div>
  @endif
</div>

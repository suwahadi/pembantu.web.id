<div class="max-w-4xl mx-auto space-y-4">
 @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl p-3 text-sm">
      {{ session('success') }}
    </div>
 @endif

  <x-card title="Detail Pesanan" subtitle="Informasi order dan status terkini.">
    @if(!$order)
      <div class="text-sm text-gray-600 dark:text-gray-400">Order tidak ditemukan.</div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-gray-600 dark:text-gray-400">Kode:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->code }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Status:</span> <x-badge variant="blue">{{ $order->status }}</x-badge></div>
        <div><span class="text-gray-600 dark:text-gray-400">Agency:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->agency_name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Worker:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->worker_name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Total:</span> <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Skema:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->contract_scheme ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Periode:</span>
          <span class="font-medium text-gray-900 dark:text-white">{{ $order->contract_start_date ?? '-' }}</span> s/d <span class="font-medium text-gray-900 dark:text-white">{{ $order->contract_end_date ?? '-' }}</span>
        </div>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <a class="text-sm text-blue-600 dark:text-blue-400 hover:underline" href="{{ route('orders.list') }}">Kembali ke Order</a>

        @if($order->status === 'pending_payment')
          <a href="/pembayaran/{{ $order->id }}" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm rounded-lg hover:bg-blue-700 transition-colors">
            Bayar Sekarang
          </a>
        @endif

        @if($order->status === 'in_progress')
          <x-button size="sm" wire:click="markCompleted" wire:loading.attr="disabled">
            <span wire:loading.remove>Konfirmasi Selesai</span>
            <span wire:loading>Memproses...</span>
          </x-button>
        @endif
      </div>
    @endif
  </x-card>

  @if($order)
    <x-card title="Timeline" subtitle="Riwayat event order (maks 50 terbaru).">
      @if(empty($events))
        <div class="text-sm text-gray-600 dark:text-gray-400">Belum ada event.</div>
      @else
        <x-order.timeline :events="$events" />
      @endif
    </x-card>
  @endif
</div>

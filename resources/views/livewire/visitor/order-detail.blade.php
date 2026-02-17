<div class="max-w-4xl mx-auto space-y-4">
  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  <x-card title="Detail Pesanan" subtitle="Informasi order dan status terkini.">
    @if(!$order)
      <div class="text-sm text-gray-600">Order tidak ditemukan.</div>
    @else
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-gray-600">Kode:</span> <span class="font-medium">{{ $order->code }}</span></div>
        <div><span class="text-gray-600">Status:</span> <x-badge variant="blue">{{ $order->status }}</x-badge></div>
        <div><span class="text-gray-600">Agency:</span> <span class="font-medium">{{ $order->agency_name ?? '-' }}</span></div>
        <div><span class="text-gray-600">Worker:</span> <span class="font-medium">{{ $order->worker_name ?? '-' }}</span></div>
        <div><span class="text-gray-600">Total:</span> <span class="font-medium">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span></div>
        <div><span class="text-gray-600">Skema:</span> <span class="font-medium">{{ $order->contract_scheme ?? '-' }}</span></div>
        <div><span class="text-gray-600">Periode:</span>
          <span class="font-medium">{{ $order->start_date ?? '-' }}</span> s/d <span class="font-medium">{{ $order->end_date ?? '-' }}</span>
        </div>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        <a class="text-sm text-blue-600 hover:underline" href="{{ route('orders.list') }}">Kembali ke Order</a>

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
        <div class="text-sm text-gray-600">Belum ada event.</div>
      @else
        <x-order.timeline :events="$events" />
      @endif
    </x-card>
  @endif
</div>

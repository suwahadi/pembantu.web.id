<div class="max-w-4xl mx-auto space-y-4">
  @if(session('success'))
    <x-card><div class="text-sm text-green-700">{{ session('success') }}</div></x-card>
  @endif
  @if(session('error'))
    <x-card><div class="text-sm text-red-700">{{ session('error') }}</div></x-card>
  @endif

  @if(!$order)
    <x-card><div class="text-sm text-gray-600">Order tidak ditemukan.</div></x-card>
  @else
    <x-card title="Detail Order" subtitle="Informasi lengkap dan timeline pekerjaan.">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-gray-600">Kode:</span> <span class="font-medium">{{ $order->code }}</span></div>
        <div><span class="text-gray-600">Status:</span> <x-badge variant="blue">{{ $order->status }}</x-badge></div>
        <div><span class="text-gray-600">Visitor:</span> <span class="font-medium">{{ $order->visitor_name ?? '-' }}</span></div>
        <div><span class="text-gray-600">Worker:</span> <span class="font-medium">{{ $order->worker_name }}</span></div>
        <div><span class="text-gray-600">Skema:</span> <span class="font-medium">{{ $order->scheme }}</span></div>
        <div><span class="text-gray-600">Total:</span> <span class="font-medium">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span></div>
        <div><span class="text-gray-600">Mulai:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($order->contract_start_date)->translatedFormat('d F Y') }}</span></div>
        <div><span class="text-gray-600">Selesai:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($order->contract_end_date)->translatedFormat('d F Y') }}</span></div>
      </div>

      <div class="mt-4 flex flex-wrap gap-2">
        @if($order->status === 'paid_escrow')
          <x-button variant="primary" wire:click="startJob" wire:loading.attr="disabled">
            <span wire:loading.remove>Mulai Pekerjaan</span>
            <span wire:loading>Memproses...</span>
          </x-button>
        @elseif($order->status === 'in_progress')
          <x-button variant="primary" wire:click="finishJob" wire:loading.attr="disabled">
            <span wire:loading.remove>Tandai Selesai</span>
            <span wire:loading>Memproses...</span>
          </x-button>
        @endif

        <a href="{{ route('agency.orders.index') }}" class="underline text-sm">Kembali ke List</a>
      </div>
    </x-card>

    <x-card title="Timeline" subtitle="Riwayat event order.">
      @if(empty($events))
        <div class="text-sm text-gray-600">Belum ada event.</div>
      @else
        <x-order.timeline :events="$events" />
      @endif
    </x-card>
  @endif
</div>

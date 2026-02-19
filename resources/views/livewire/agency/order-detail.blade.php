<div class="max-w-4xl mx-auto space-y-4">
  @if(session('success'))
    <x-card><div class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div></x-card>
  @endif
  @if(session('error'))
    <x-card><div class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div></x-card>
  @endif

  @if(!$order)
    <x-card><div class="text-sm text-gray-600 dark:text-gray-400">Order tidak ditemukan.</div></x-card>
  @else
    <x-card title="Detail Order" subtitle="Informasi lengkap dan timeline pekerjaan.">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
        <div><span class="text-gray-600 dark:text-gray-400">Kode:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->code }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Status:</span> <x-badge variant="blue">{{ $order->status }}</x-badge></div>
        <div><span class="text-gray-600 dark:text-gray-400">Visitor:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->visitor_name ?? '-' }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Worker:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->worker_name }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Skema:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $order->scheme }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Total:</span> <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($order->total_idr, 0, ',', '.') }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Mulai:</span> <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($order->contract_start_date)->translatedFormat('d F Y') }}</span></div>
        <div><span class="text-gray-600 dark:text-gray-400">Selesai:</span> <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($order->contract_end_date)->translatedFormat('d F Y') }}</span></div>
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

        <a href="{{ route('agency.orders.index') }}" class="underline text-sm text-blue-600 dark:text-blue-400">Kembali ke List</a>
      </div>
    </x-card>

    <x-card title="Timeline" subtitle="Riwayat event order.">
      @if(empty($events))
        <div class="text-sm text-gray-600 dark:text-gray-400">Belum ada event.</div>
      @else
        <x-order.timeline :events="$events" />
      @endif
    </x-card>
  @endif
</div>

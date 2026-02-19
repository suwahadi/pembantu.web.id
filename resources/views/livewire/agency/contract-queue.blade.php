<div class="space-y-4">
  <x-card title="Kontrak Menunggu Tanda Tangan" subtitle="Review dan tandatangani kontrak dari visitor.">
    @if($items->isEmpty())
      <div class="text-sm text-gray-600 dark:text-gray-400">Tidak ada kontrak menunggu.</div>
    @else
      <div class="overflow-auto border rounded-xl dark:border-gray-700">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="text-left p-3 text-gray-700 dark:text-gray-300">Worker</th>
              <th class="text-left p-3 text-gray-700 dark:text-gray-300">Skema</th>
              <th class="text-left p-3 text-gray-700 dark:text-gray-300">Periode</th>
              <th class="text-left p-3 text-gray-700 dark:text-gray-300">Total</th>
              <th class="text-left p-3 text-gray-700 dark:text-gray-300">Dibuat</th>
              <th class="text-left p-3 text-gray-700 dark:text-gray-300">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
              <tr class="border-t dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800">
                <td class="p-3 font-medium text-gray-900 dark:text-white">{{ $item->worker_name ?? '-' }}</td>
                <td class="p-3 text-gray-900 dark:text-white">{{ $item->scheme }}</td>
                <td class="p-3 text-xs text-gray-900 dark:text-white">
                  {{ \Carbon\Carbon::parse($item->start_date)->format('d/m') }} -
                  {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                </td>
                <td class="p-3 text-gray-900 dark:text-white">Rp {{ number_format($item->total_idr, 0, ',', '.') }}</td>
                <td class="p-3 text-xs text-gray-600 dark:text-gray-400">
                  {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}
                </td>
                <td class="p-3">
                  <button
                    wire:click="select({{ $item->id }})"
                    class="text-xs text-blue-600 dark:text-blue-400 hover:underline"
                  >
                    Review
                  </button>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>

      @if(method_exists($items, 'links'))
        <div class="mt-3">
          {{ $items->links() }}
        </div>
      @endif
    @endif
  </x-card>

  @if($selected)
    <x-card title="Detail Kontrak" subtitle="Tanda tangani kontrak ini untuk menerima order">
      <div class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div><span class="text-gray-600 dark:text-gray-400">Worker:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $selected->worker_name ?? '-' }}</span></div>
          <div><span class="text-gray-600 dark:text-gray-400">Skema:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $selected->scheme }}</span></div>
          <div><span class="text-gray-600 dark:text-gray-400">Mulai:</span> <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($selected->start_date)->translatedFormat('l, d F Y') }}</span></div>
          <div><span class="text-gray-600 dark:text-gray-400">Selesai:</span> <span class="font-medium text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($selected->end_date)->translatedFormat('l, d F Y') }}</span></div>
          <div><span class="text-gray-600 dark:text-gray-400">Total:</span> <span class="font-medium text-gray-900 dark:text-white">Rp {{ number_format($selected->total_idr, 0, ',', '.') }}</span></div>
          <div><span class="text-gray-600 dark:text-gray-400">Durasi Jam:</span> <span class="font-medium text-gray-900 dark:text-white">{{ $selected->estimated_hours ?? '-' }} jam</span></div>
        </div>

        <div class="bg-gray-50 dark:bg-gray-800 border dark:border-gray-700 rounded-lg p-3 text-sm max-h-64 overflow-auto">
          <div class="font-medium mb-2 text-gray-900 dark:text-white">Deskripsi Pekerjaan:</div>
          <div class="whitespace-pre-wrap text-gray-700 dark:text-gray-300">{{ $selected->description ?? '-' }}</div>
        </div>

        <div class="flex gap-2">
          <x-button
            variant="primary"
            wire:click="sign"
            wire:loading.attr="disabled"
          >
            <span wire:loading.remove>Tandatangani Kontrak</span>
            <span wire:loading>Memproses...</span>
          </x-button>

          <x-button
            variant="secondary"
            wire:click="$set('selectedId', null)"
          >
            Batal
          </x-button>
        </div>
      </div>
    </x-card>
  @endif
</div>

<div class="space-y-4">
  <x-card title="Kontrak Menunggu Tanda Tangan" subtitle="Review dan tandatangani kontrak dari visitor.">
    @if($items->isEmpty())
      <div class="text-sm text-gray-600">Tidak ada kontrak menunggu.</div>
    @else
      <div class="overflow-auto border rounded-xl">
        <table class="w-full text-sm">
          <thead class="bg-gray-50">
            <tr>
              <th class="text-left p-3">Worker</th>
              <th class="text-left p-3">Skema</th>
              <th class="text-left p-3">Periode</th>
              <th class="text-left p-3">Total</th>
              <th class="text-left p-3">Dibuat</th>
              <th class="text-left p-3">Aksi</th>
            </tr>
          </thead>
          <tbody>
            @foreach($items as $item)
              <tr class="border-t hover:bg-gray-50">
                <td class="p-3 font-medium">{{ $item->worker_name ?? '-' }}</td>
                <td class="p-3">{{ $item->scheme }}</td>
                <td class="p-3 text-xs">
                  {{ \Carbon\Carbon::parse($item->start_date)->format('d/m') }} -
                  {{ \Carbon\Carbon::parse($item->end_date)->format('d/m/Y') }}
                </td>
                <td class="p-3">Rp {{ number_format($item->total_idr, 0, ',', '.') }}</td>
                <td class="p-3 text-xs text-gray-600">
                  {{ \Carbon\Carbon::parse($item->created_at)->translatedFormat('d F Y') }}
                </td>
                <td class="p-3">
                  <button
                    wire:click="select({{ $item->id }})"
                    class="text-xs text-blue-600 hover:underline"
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
          <div><span class="text-gray-600">Worker:</span> <span class="font-medium">{{ $selected->worker_name ?? '-' }}</span></div>
          <div><span class="text-gray-600">Skema:</span> <span class="font-medium">{{ $selected->scheme }}</span></div>
          <div><span class="text-gray-600">Mulai:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($selected->start_date)->translatedFormat('l, d F Y') }}</span></div>
          <div><span class="text-gray-600">Selesai:</span> <span class="font-medium">{{ \Carbon\Carbon::parse($selected->end_date)->translatedFormat('l, d F Y') }}</span></div>
          <div><span class="text-gray-600">Total:</span> <span class="font-medium">Rp {{ number_format($selected->total_idr, 0, ',', '.') }}</span></div>
          <div><span class="text-gray-600">Durasi Jam:</span> <span class="font-medium">{{ $selected->estimated_hours ?? '-' }} jam</span></div>
        </div>

        <div class="bg-gray-50 border rounded-lg p-3 text-sm max-h-64 overflow-auto">
          <div class="font-medium mb-2">Deskripsi Pekerjaan:</div>
          <div class="whitespace-pre-wrap">{{ $selected->description ?? '-' }}</div>
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

<div class="space-y-4">
  <x-card title="Order Management" subtitle="Kelola order yang sedang berjalan.">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
      <x-form.input label="Cari Kode / Worker / Visitor" wire:model.live="q" placeholder="..." />
      <x-form.select
        label="Status"
        wire:model.live="status"
        :options="['' => '- Semua Status -'] + $statuses"
      />
    </div>

    <div class="mt-4 overflow-auto border rounded-xl">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-3">Kode</th>
            <th class="text-left p-3">Worker</th>
            <th class="text-left p-3">Visitor</th>
            <th class="text-left p-3">Periode</th>
            <th class="text-left p-3">Total</th>
            <th class="text-left p-3">Status</th>
            <th class="p-3"></th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $o)
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3 font-medium">{{ $o->code }}</td>
              <td class="p-3">{{ $o->worker_name }}</td>
              <td class="p-3">{{ $o->visitor_name ?? '-' }}</td>
              <td class="p-3 text-xs">
                {{ \Carbon\Carbon::parse($o->contract_start_date)->format('d/m') }} -
                {{ \Carbon\Carbon::parse($o->contract_end_date)->format('d/m/Y') }}
              </td>
              <td class="p-3">Rp {{ number_format($o->total_idr, 0, ',', '.') }}</td>
              <td class="p-3">
                <x-badge variant="blue">{{ $o->status }}</x-badge>
              </td>
              <td class="p-3 text-right">
                <a href="{{ route('agency.orders.show', $o->id) }}" class="underline text-sm">Detail</a>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="p-3 text-center text-gray-600 text-sm">Tidak ada order</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $items->links() }}</div>
  </x-card>
</div>

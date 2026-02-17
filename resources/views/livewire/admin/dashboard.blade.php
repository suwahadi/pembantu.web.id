<div class="space-y-4">
  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <x-card title="Dispute Aktif">
      <div class="text-3xl font-semibold">{{ $kpi['dispute_open'] }}</div>
      <div class="text-sm text-gray-600">Open + Investigating</div>
    </x-card>

    <x-card title="Refund Antrian">
      <div class="text-3xl font-semibold">{{ $kpi['refund_queued'] }}</div>
      <div class="text-sm text-gray-600">Queued + Processing</div>
    </x-card>

    <x-card title="Payout Antrian">
      <div class="text-3xl font-semibold">{{ $kpi['payout_queued'] }}</div>
      <div class="text-sm text-gray-600">Queued + Processing</div>
    </x-card>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <x-card title="Order Paid (Escrow)">
      <div class="text-3xl font-semibold">{{ $kpi['order_paid_escrow'] }}</div>
    </x-card>
    <x-card title="Order Berjalan">
      <div class="text-3xl font-semibold">{{ $kpi['order_in_progress'] }}</div>
    </x-card>
    <x-card title="Order Selesai">
      <div class="text-3xl font-semibold">{{ $kpi['order_completed'] }}</div>
    </x-card>
  </div>

  <x-card title="Order Terbaru" subtitle="10 data terakhir">
    <div class="overflow-auto border rounded-xl">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-3">Kode</th>
            <th class="text-left p-3">Status</th>
            <th class="text-left p-3">Total</th>
            <th class="text-left p-3">Tanggal</th>
          </tr>
        </thead>
        <tbody>
          @foreach($latest as $o)
            <tr class="border-t">
              <td class="p-3 font-medium">{{ $o->code }}</td>
              <td class="p-3"><x-badge variant="blue">{{ $o->status }}</x-badge></td>
              <td class="p-3">Rp {{ number_format($o->total_idr, 0, ',', '.') }}</td>
              <td class="p-3">{{ \Carbon\Carbon::parse($o->created_at)->translatedFormat('l, d F Y') }}</td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </x-card>
</div>

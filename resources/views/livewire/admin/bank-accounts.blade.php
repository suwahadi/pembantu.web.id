<div class="space-y-4">
  <x-card title="Filter Rekening" subtitle="Cari, filter berdasarkan owner dan status verifikasi">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <x-form.input
        label="Cari"
        wire:model.live="q"
        placeholder="Bank, nomor, nama..."
      />

      <x-form.select
        label="Owner Type"
        :value="$ownerType"
        wire:model.live="ownerType"
        :options="['' => '- Semua -', 'USER' => 'User', 'AGENCY' => 'Agency']"
      />

      <x-form.select
        label="Status Verifikasi"
        :value="$verifiedStatus"
        wire:model.live="verifiedStatus"
        :options="['' => '- Semua -', 'pending' => 'Pending', 'verified' => 'Verified', 'rejected' => 'Rejected']"
      />
    </div>
  </x-card>

  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-800 rounded-xl p-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  <x-card title="Daftar Rekening">
    <div class="overflow-auto border rounded-xl">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-3">Bank</th>
            <th class="text-left p-3">Nomor Rekening</th>
            <th class="text-left p-3">Nama Rekening</th>
            <th class="text-left p-3">Owner</th>
            <th class="text-left p-3">Status</th>
            <th class="text-left p-3">Aksi</th>
          </tr>
        </thead>
        <tbody>
          @forelse($items as $item)
            <tr class="border-t hover:bg-gray-50">
              <td class="p-3">{{ $item->bank_name }}</td>
              <td class="p-3 font-mono text-xs">{{ $item->account_no }}</td>
              <td class="p-3">{{ $item->account_name }}</td>
              <td class="p-3 text-xs">
                <span>{{ $item->owner_type === 'USER' ? 'User' : 'Agency' }}</span>
                <div class="text-gray-600">ID: {{ $item->owner_id }}</div>
              </td>
              <td class="p-3">
                @if($item->verified_status === 'pending')
                  <x-badge variant="yellow">Pending</x-badge>
                @elseif($item->verified_status === 'verified')
                  <x-badge variant="green">Verified</x-badge>
                @else
                  <x-badge variant="red">Rejected</x-badge>
                @endif
              </td>
              <td class="p-3">
                <button
                  wire:click="select({{ $item->id }})"
                  class="text-xs text-blue-600 hover:underline"
                >
                  Verifikasi
                </button>
              </td>
            </tr>
          @empty
            <tr>
              <td colspan="6" class="p-3 text-center text-gray-600 text-sm">Tidak ada data</td>
            </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="mt-3">
      {{ $items->links() }}
    </div>
  </x-card>

  @if($selected)
    <x-card title="Verifikasi Rekening" subtitle="Status: {{ $selected->verified_status }}">
      <div class="space-y-3">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-3 text-sm">
          <div><span class="text-gray-600">Bank:</span> <span class="font-medium">{{ $selected->bank_name }}</span></div>
          <div><span class="text-gray-600">Nomor:</span> <span class="font-mono">{{ $selected->account_no }}</span></div>
          <div><span class="text-gray-600">Nama:</span> <span class="font-medium">{{ $selected->account_name }}</span></div>
          <div><span class="text-gray-600">Owner:</span> <span class="font-medium">{{ $selected->owner_type === 'USER' ? 'User' : 'Agency' }} (ID: {{ $selected->owner_id }})</span></div>
        </div>

        <x-form.select
          label="Status Verifikasi"
          :value="$setStatus"
          wire:model="setStatus"
          :options="['verified' => 'Verified', 'rejected' => 'Rejected', 'pending' => 'Pending']"
        />

        <x-form.textarea
          label="Catatan Admin"
          :value="$adminNote"
          wire:model="adminNote"
          placeholder="Alasan jika ditolak..."
          :rows="3"
        />

        <div class="flex gap-2">
          <x-button
            variant="primary"
            wire:click="verify"
            wire:loading.attr="disabled"
          >
            <span wire:loading.remove>Simpan Status</span>
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

<div class="max-w-2xl mx-auto space-y-4">
  @if(session('success'))
    <div class="bg-green-50 border border-green-200 text-green-700 rounded-xl p-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  <x-card title="Rekening Bank Agency" subtitle="Kelola rekening untuk menerima payout">
    @if(empty($items->toArray()))
      <div class="text-sm text-gray-600">Agency belum menambahkan rekening bank.</div>
    @else
      <div class="space-y-2">
        @foreach($items as $item)
          <div class="flex items-center justify-between p-3 border rounded-lg {{ $primaryId === $item->id ? 'bg-blue-50 border-blue-200' : 'hover:bg-gray-50' }}">
            <div class="flex-1">
              <div class="font-medium text-sm">{{ $item->bank_name }}</div>
              <div class="text-xs text-gray-600">{{ $item->account_no }} • {{ $item->account_name }}</div>
              <div class="text-xs mt-1">
                @if($item->verified_status === 'verified')
                  <x-badge variant="green">Verified</x-badge>
                @elseif($item->verified_status === 'rejected')
                  <x-badge variant="red">Rejected</x-badge>
                @else
                  <x-badge variant="yellow">Pending</x-badge>
                @endif
              </div>
            </div>

            <div class="flex items-center gap-2">
              @if($item->verified_status === 'verified' && $primaryId !== $item->id)
                <x-button
                  size="sm"
                  variant="secondary"
                  wire:click="setPrimary({{ $item->id }})"
                  wire:loading.attr="disabled"
                >
                  <span wire:loading.remove>Set Utama</span>
                  <span wire:loading>Proses...</span>
                </x-button>
              @elseif($primaryId === $item->id)
                <span class="text-xs font-medium text-blue-600 flex items-center gap-1">
                  @include('svgs.icon-check', ['class' => 'w-4 h-4 text-blue-600'])
                  Rekening Utama
                </span>
              @endif
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </x-card>

  <x-card title="Tambah Rekening Baru">
    <div class="space-y-3">
      <x-form.input
        label="Nama Bank"
        wire:model="bankName"
        placeholder="BCA, Mandiri, BNI, dll"
        @error('bankName') :error="$errors->first('bankName')" @enderror
      />

      <x-form.input
        label="Nomor Rekening"
        wire:model="accountNo"
        placeholder="Contoh: 123456789012"
        @error('accountNo') :error="$errors->first('accountNo')" @enderror
      />

      <x-form.input
        label="Nama Pemilik Rekening"
        wire:model="accountName"
        placeholder="Nama sesuai buku tabungan"
        @error('accountName') :error="$errors->first('accountName')" @enderror
      />

      <x-button
        variant="primary"
        wire:click="add"
        wire:loading.attr="disabled"
      >
        <span wire:loading.remove>Tambah Rekening</span>
        <span wire:loading>Memproses...</span>
      </x-button>
    </div>

    <div class="mt-3 p-3 bg-yellow-50 border border-yellow-200 text-yellow-800 rounded-lg text-xs flex items-start gap-2">
      @include('svgs.icon-warning', ['class' => 'w-4 h-4 flex-shrink-0 mt-0.5'])
      <span>Rekening baru akan melalui verifikasi oleh admin sebelum dapat digunakan sebagai rekening utama untuk menerima payout.</span>
    </div>
  </x-card>
</div>

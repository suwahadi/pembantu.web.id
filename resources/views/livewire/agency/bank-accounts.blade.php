<div class="max-w-2xl mx-auto space-y-4">
  @if(session('success'))
    <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl p-3 text-sm">
      {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl p-3 text-sm">
      {{ session('error') }}
    </div>
  @endif

  @if ($errors->any())
    <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl p-3 text-sm">
      <ul class="list-disc list-inside space-y-1">
        @foreach ($errors->all() as $error)
          <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif

  <x-card title="Rekening Bank Agency" subtitle="Kelola rekening untuk menerima payout">
    @if(empty($items->toArray()))
      <div class="text-sm text-gray-600 dark:text-gray-400">Agency belum menambahkan rekening bank.</div>
    @else
      <div class="space-y-2">
        @foreach($items as $item)
          <div class="flex items-center justify-between p-3 border rounded-lg dark:border-gray-700 {{ $primaryId === $item->id ? 'bg-blue-50 dark:bg-blue-900/30 border-blue-200 dark:border-blue-800' : 'hover:bg-gray-50 dark:hover:bg-gray-800' }}">
            <div class="flex-1">
              <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $item->bank_name }}</div>
              <div class="text-xs text-gray-600 dark:text-gray-400">{{ $item->account_no }} • {{ $item->account_name }}</div>
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
                <span class="text-xs font-medium text-blue-600 dark:text-blue-400 flex items-center gap-1">
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
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Kode Bank</label>
        <input 
          type="text"
          wire:model.blur="bankCode"
          placeholder="BCA, MANDIRI, BNI, CIMB, dll"
          class="w-full px-3 py-2 border @error('bankCode') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        @error('bankCode')
          <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Bank</label>
        <input 
          type="text"
          wire:model.blur="bankName"
          placeholder="PT Bank BCA Indonesia"
          class="w-full px-3 py-2 border @error('bankName') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        @error('bankName')
          <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nomor Rekening</label>
        <input 
          type="text"
          wire:model.blur="accountNo"
          placeholder="Contoh: 123456789012"
          class="w-full px-3 py-2 border @error('accountNo') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        @error('accountNo')
          <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Nama Pemilik Rekening</label>
        <input 
          type="text"
          wire:model.blur="accountName"
          placeholder="Nama sesuai buku tabungan"
          class="w-full px-3 py-2 border @error('accountName') border-red-500 @else border-gray-300 dark:border-gray-600 @enderror rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500"
        />
        @error('accountName')
          <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      <button 
        type="button"
        wire:click="add"
        wire:loading.attr="disabled"
        class="px-4 py-2 rounded-lg font-medium text-sm bg-blue-600 dark:bg-blue-800 text-white hover:bg-blue-700 dark:hover:bg-blue-900 disabled:bg-gray-400 transition-colors"
      >
        <span wire:loading.remove>Tambah Rekening</span>
        <span wire:loading>Memproses...</span>
      </button>
    </div>

    <div class="mt-3 p-3 bg-yellow-50 dark:bg-yellow-900/30 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-lg text-xs flex items-start gap-2">
      @include('svgs.icon-warning', ['class' => 'w-4 h-4 flex-shrink-0 mt-0.5'])
      <span>Rekening baru akan melalui verifikasi oleh admin sebelum dapat digunakan sebagai rekening utama untuk menerima payout.</span>
    </div>
  </x-card>
</div>

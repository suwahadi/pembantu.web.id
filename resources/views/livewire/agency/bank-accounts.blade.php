<div class="max-w-2xl mx-auto space-y-6">
    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-xl p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl p-4 text-sm">
            {{ session('error') }}
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 text-red-700 dark:text-red-300 rounded-xl p-4 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Rekening Bank Agency</h3>
            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Kelola rekening untuk menerima payout</p>
        </div>

        @if($items->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                </svg>
                <p class="text-sm text-gray-500 dark:text-gray-400">Belum ada rekening bank terdaftar</p>
            </div>
        @else
            <div class="space-y-3">
                @foreach($items as $item)
                    <div class="flex items-center justify-between p-4 border rounded-xl transition-colors {{ $primaryId === $item->id ? 'bg-primary-50 dark:bg-primary-900/20 border-primary-200 dark:border-primary-800' : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800' }}">
                        <div class="flex-1 min-w-0">
                            <div class="font-medium text-sm text-gray-900 dark:text-white">{{ $item->bank_name }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $item->account_no }} &bull; {{ $item->account_name }}</div>
                            <div class="mt-2">
                                @if($item->verified_status === 'verified')
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">Verified</span>
                                @elseif($item->verified_status === 'rejected')
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">Rejected</span>
                                @else
                                    <span class="px-2 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400">Pending</span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center gap-2 ml-4">
                            @if($item->verified_status === 'verified' && $primaryId !== $item->id)
                                <button wire:click="setPrimary({{ $item->id }})" wire:loading.attr="disabled"
                                    class="px-3 py-1.5 rounded-lg text-xs font-medium border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                    <span wire:loading.remove wire:target="setPrimary({{ $item->id }})">Set Utama</span>
                                    <span wire:loading wire:target="setPrimary({{ $item->id }})">Proses...</span>
                                </button>
                            @elseif($primaryId === $item->id)
                                <span class="text-xs font-medium text-primary-600 dark:text-primary-400 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Rekening Utama
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="mb-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Tambah Rekening Baru</h3>
        </div>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Kode Bank</label>
                <input type="text" wire:model.blur="bankCode" placeholder="BCA, MANDIRI, BNI, CIMB, dll"
                    class="w-full h-10 px-3 py-2 text-sm border @error('bankCode') border-red-500 @else border-gray-200 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" />
                @error('bankCode') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Bank</label>
                <input type="text" wire:model.blur="bankName" placeholder="PT Bank BCA Indonesia"
                    class="w-full h-10 px-3 py-2 text-sm border @error('bankName') border-red-500 @else border-gray-200 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" />
                @error('bankName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nomor Rekening</label>
                <input type="text" wire:model.blur="accountNo" placeholder="Contoh: 123456789012"
                    class="w-full h-10 px-3 py-2 text-sm border @error('accountNo') border-red-500 @else border-gray-200 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" />
                @error('accountNo') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nama Pemilik Rekening</label>
                <input type="text" wire:model.blur="accountName" placeholder="Nama sesuai buku tabungan"
                    class="w-full h-10 px-3 py-2 text-sm border @error('accountName') border-red-500 @else border-gray-200 dark:border-gray-600 @enderror rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent" />
                @error('accountName') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
            </div>

            <button type="button" wire:click="add" wire:loading.attr="disabled"
                class="px-5 py-2.5 rounded-lg font-medium text-sm bg-primary-600 text-white hover:bg-primary-700 disabled:bg-gray-400 transition-colors">
                <span wire:loading.remove wire:target="add">Tambah Rekening</span>
                <span wire:loading wire:target="add">Memproses...</span>
            </button>
        </div>

        <div class="mt-4 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 text-yellow-800 dark:text-yellow-200 rounded-xl text-xs flex items-start gap-2">
            <svg class="w-4 h-4 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4.832c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
            <span>Rekening baru akan melalui verifikasi oleh admin sebelum dapat digunakan sebagai rekening utama untuk menerima payout.</span>
        </div>
    </div>
</div>

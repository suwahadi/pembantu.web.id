<div class="space-y-6">
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 md:p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Rekening Bank</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Verifikasi dan kelola rekening bank</p>
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Cari</label>
                <input type="text"
                    wire:model.live.debounce.300ms="q"
                    placeholder="Bank, nomor, nama..."
                    class="w-full h-10 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Owner</label>
                <select wire:model.live="ownerType"
                    class="w-full h-10 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="USER">User</option>
                    <option value="AGENCY">Agency</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Verifikasi</label>
                <select wire:model.live="verifiedStatus"
                    class="w-full h-10 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                    <option value="">Semua</option>
                    <option value="unverified">Unverified</option>
                    <option value="verified">Verified</option>
                    <option value="rejected">Rejected</option>
                </select>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-green-700 dark:text-green-300 rounded-lg p-4 text-sm">
            {{ session('success') }}
        </div>
    @endif

    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-800">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800/50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Bank</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">No. Rekening</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama Rekening</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Owner</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="bg-white dark:bg-gray-900 divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($items as $item)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">{{ $item->bank_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-mono text-gray-900 dark:text-white">{{ $item->account_no }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-white">{{ $item->account_name }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                @php $isAgency = str_contains(strtolower($item->owner_type), 'agency'); @endphp
                                <span class="px-2 py-1 inline-flex text-xs font-medium rounded-full {{ $isAgency ? 'bg-indigo-100 text-indigo-800 dark:bg-indigo-500/20 dark:text-indigo-400' : 'bg-gray-100 text-gray-800 dark:bg-gray-500/20 dark:text-gray-400' }}">
                                    {{ $isAgency ? 'Agency' : 'User' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($item->verified_status === 'verified')
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">Verified</span>
                                @elseif($item->verified_status === 'rejected')
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">Rejected</span>
                                @else
                                    <span class="px-2 py-1 inline-flex text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800 dark:bg-yellow-500/20 dark:text-yellow-400">Unverified</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center">
                                <button wire:click="select({{ $item->id }})"
                                    class="text-sm font-medium text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300">
                                    Verifikasi
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <svg class="h-12 w-12 text-gray-400 mx-auto mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Tidak ada rekening ditemukan</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($items->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-700">
                {{ $items->links() }}
            </div>
        @endif
    </div>

    @if($selected)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-700 dark:bg-gray-800 md:p-6">
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90 mb-4">Verifikasi Rekening</h3>
            <div class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Bank:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $selected->bank_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">No. Rekening:</span>
                        <span class="ml-2 font-mono text-gray-900 dark:text-white">{{ $selected->account_no }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Nama:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ $selected->account_name }}</span>
                    </div>
                    <div>
                        <span class="text-gray-500 dark:text-gray-400">Owner:</span>
                        <span class="ml-2 font-medium text-gray-900 dark:text-white">{{ str_contains(strtolower($selected->owner_type), 'agency') ? 'Agency' : 'User' }} (ID: {{ $selected->owner_id }})</span>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Status Verifikasi</label>
                    <select wire:model="setStatus"
                        class="w-full max-w-xs h-10 px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                        <option value="verified">Verified</option>
                        <option value="rejected">Rejected</option>
                        <option value="unverified">Unverified</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Catatan Admin</label>
                    <textarea wire:model="adminNote"
                        rows="3"
                        placeholder="Alasan jika ditolak..."
                        class="w-full px-3 py-2 text-sm border border-gray-200 rounded-lg bg-white dark:bg-gray-800 dark:border-gray-600 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent"></textarea>
                </div>

                <div class="flex gap-3 pt-2">
                    <button wire:click="verify" wire:loading.attr="disabled"
                        class="px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50">
                        <span wire:loading.remove>Simpan Status</span>
                        <span wire:loading>Memproses...</span>
                    </button>
                    <button wire:click="$set('selectedId', null)"
                        class="px-4 py-2 bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 text-sm font-medium rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>

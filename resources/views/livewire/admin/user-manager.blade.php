<div class="space-y-5">
    @if($alert)
        @php
            $alertClasses = $alert['type'] === 'success'
                ? 'bg-success-50 text-success-800 border-success-200'
                : 'bg-error-50 text-error-800 border-error-200';
        @endphp
        <div x-data="{ show: true }"
             x-init="setTimeout(() => { show = false; $wire.clearAlert(); }, 3500)"
             x-show="show"
             x-transition
             class="fixed inset-x-0 top-4 z-50 mx-auto max-w-xl px-4">
            <div class="flex items-start justify-between rounded-xl border px-4 py-3 shadow-lg {{ $alertClasses }}">
                <p class="text-sm font-medium">{{ $alert['message'] }}</p>
                <button type="button" class="ml-4 text-xs underline" @click="show = false; $wire.clearAlert();">Tutup</button>
            </div>
        </div>
    @endif

    <!-- Filters Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-6">
        <div class="flex flex-col gap-6 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Manajemen Users</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400">Kelola data user, status akun, dan role.</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="clearFilters" type="button" class="px-4 py-2 text-sm border border-gray-200 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-300 hover:bg-gray-50 dark:hover:bg-slate-800 transition-colors">Reset Filter</button>
            </div>
        </div>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Cari User</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-4.35-4.35M10 18a8 8 0 100-16 8 8 0 000 16z" />
                        </svg>
                    </div>
                    <input type="text" wire:model.live.debounce.400ms="search" placeholder="Nama, email, atau nomor" class="w-full pl-10 pr-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-white dark:bg-slate-800 dark:border-slate-600 text-gray-900 dark:text-slate-50 placeholder:text-gray-400 dark:placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent" />
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Status</label>
                <select wire:model.live="status" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-white dark:bg-slate-800 dark:border-slate-600 text-gray-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    <option value="">Semua Status</option>
                    @foreach($statuses as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">Role</label>
                <select wire:model.live="role" class="w-full px-3 py-2.5 text-sm border border-gray-200 rounded-lg bg-white dark:bg-slate-800 dark:border-slate-600 text-gray-900 dark:text-slate-50 focus:outline-none focus:ring-2 focus:ring-brand-500 focus:border-transparent">
                    <option value="">Semua Role</option>
                    @foreach($roles as $roleOption)
                        <option value="{{ $roleOption->id }}">{{ $roleOption->label ?? $roleOption->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-slate-800 dark:bg-slate-900">
        <div class="overflow-x-auto">
            <table class="min-w-[720px] w-full divide-y divide-gray-200 dark:divide-slate-700">
                <thead class="bg-gray-50 dark:bg-slate-800/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">User</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Kontak</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-slate-400 uppercase tracking-wider">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white dark:divide-slate-800 dark:bg-slate-900">
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-slate-800/80">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-brand-100 dark:bg-brand-500/20 flex items-center justify-center text-brand-700 dark:text-white font-semibold">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900 dark:text-slate-50">{{ $user->name }}</div>
                                        <div class="text-xs text-gray-500 dark:text-slate-400">Bergabung {{ $user->created_at?->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                <p class="text-gray-900 dark:text-slate-50">{{ $user->email }}</p>
                                <p class="text-gray-500 dark:text-slate-400 text-xs">{{ $user->phone ?: 'Belum diisi' }}</p>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-slate-50">
                                @if($user->roles->isEmpty())
                                    <span class="text-xs text-gray-500 dark:text-slate-400">-</span>
                                @else
                                    <div class="flex flex-wrap gap-1">
                                        @foreach($user->roles as $role)
                                            <span class="px-2 py-0.5 rounded-full text-xs bg-gray-100 dark:bg-slate-700 text-gray-700 dark:text-slate-200">{{ $role->label ?? ucfirst($role->name) }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <button type="button" wire:click="toggleStatus({{ $user->id }})" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold transition-colors {{ $user->status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-500/25 dark:text-green-200' : 'bg-gray-200 text-gray-700 dark:bg-slate-700 dark:text-slate-200' }}">
                                    <span class="mr-2 inline-block w-2 h-2 rounded-full {{ $user->status === 'active' ? 'bg-green-500' : 'bg-gray-400 dark:bg-slate-400' }}"></span>
                                    {{ $user->status === 'active' ? 'Aktif' : 'Nonaktif' }}
                                </button>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('admin.users.edit', $user->id) }}" class="px-3 py-1 text-xs border border-gray-200 dark:border-slate-700 rounded-lg text-gray-700 dark:text-slate-100 hover:bg-gray-50 dark:hover:bg-slate-800/80">Edit</a>
                                    <button type="button" wire:click="confirmDelete({{ $user->id }})" class="px-3 py-1 text-xs border border-red-200 text-red-600 dark:border-red-500/70 dark:text-red-300 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/30">Delete</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500 dark:text-slate-400">Belum ada data user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="border-t border-gray-200 dark:border-slate-800 px-6 py-4">
            {{ $users->links() }}
        </div>
    </div>

    <!-- Delete confirmation modal -->
    @if($confirmingDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center">
            <div class="absolute inset-0 bg-slate-900/60" aria-hidden="true" wire:click="closeModal"></div>
            <div class="relative w-full max-w-md space-y-4 rounded-2xl border border-gray-200 bg-white p-6 shadow-xl dark:border-slate-700 dark:bg-slate-900">
                <div class="flex items-center gap-3">
                    <div class="flex h-10 w-10 items-center justify-center rounded-full bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-300">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M12 18a9 9 0 100-18 9 9 0 000 18z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-base font-semibold text-gray-900 dark:text-slate-50">Hapus User?</p>
                        <p class="text-sm text-gray-500 dark:text-slate-400">Tindakan ini tidak dapat dibatalkan.</p>
                    </div>
                </div>
                <div class="flex flex-col gap-2 sm:flex-row sm:justify-end">
                    <button type="button" wire:click="closeModal" class="w-full rounded-lg border border-gray-200 px-4 py-2 text-gray-700 hover:bg-gray-50 dark:border-slate-700 dark:text-slate-200 dark:hover:bg-slate-800 sm:w-auto">Batal</button>
                    <button type="button" wire:click="deleteUser" class="w-full rounded-lg bg-red-600 px-4 py-2 text-white hover:bg-red-700 sm:w-auto">Ya, Hapus</button>
                </div>
            </div>
        </div>
    @endif
</div>

<div class="max-w-3xl mx-auto space-y-8">
    @if (session('success'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-green-800 dark:border-green-800 dark:bg-green-900/20 dark:text-green-300">
            {{ session('success') }}
        </div>
    @endif

    <form wire:submit.prevent="save" class="space-y-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Informasi User</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400">Isi data user dengan lengkap dan pastikan role sesuai.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Nama</label>
                    <input type="text" wire:model.defer="name" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Email</label>
                    <input type="email" wire:model.defer="email" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Nomor Telepon</label>
                    <input type="text" wire:model.defer="phone" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" placeholder="Opsional" />
                    @error('phone')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Status</label>
                    <select wire:model.defer="status" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50">
                        <option value="active">Aktif</option>
                        <option value="inactive">Nonaktif</option>
                    </select>
                    @error('status')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Role & Akses</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400">Pilih minimal satu role untuk user.</p>
            </div>

            <div class="grid grid-cols-1 gap-5 md:grid-cols-3">
                @foreach($roleOptions as $role)
                    <label class="flex items-center gap-3 rounded-xl border border-gray-200 bg-white px-5 py-4 text-sm font-medium text-gray-700 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-200">
                        <input type="checkbox" value="{{ $role['id'] }}" wire:model="roles" class="h-4 w-4 rounded border-gray-300 text-brand-600 focus:ring-brand-500" />
                        <span>{{ $role['label'] }}</span>
                    </label>
                @endforeach
            </div>
            @error('roles')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
        </div>

        <div class="rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-slate-700 dark:bg-slate-900/70 md:p-8">
            <div class="mb-8">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-slate-50">Kredensial</h2>
                <p class="text-sm text-gray-500 dark:text-slate-400">Password minimal 8 karakter. Kosongkan jika tidak ingin mengubah.</p>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Password</label>
                    <input type="password" wire:model.defer="password" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-slate-300">Konfirmasi Password</label>
                    <input type="password" wire:model.defer="password_confirmation" class="mt-2 w-full rounded-lg border border-gray-200 bg-white px-4 py-3 text-sm text-gray-900 focus:border-brand-500 focus:outline-none focus:ring-2 focus:ring-brand-500 dark:border-slate-600 dark:bg-slate-800 dark:text-slate-50" />
                    @error('password_confirmation')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex items-center justify-between mt-8">
            <a href="{{ route('admin.users.index') }}" class="px-4 py-2 rounded-lg border border-primary-600 bg-gray-900 text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:border-gray-300 dark:hover:bg-gray-100 transition-colors">Kembali ke Daftar</a>
            <button type="submit" class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-6 py-3 text-sm font-medium text-white shadow-lg transition-all hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:bg-brand-700 dark:hover:bg-brand-800">
                Simpan User
            </button>
        </div>
    </form>
</div>

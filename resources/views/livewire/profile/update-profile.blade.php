<div class="py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-10 text-center sm:text-left">
            <h1 class="text-4xl font-extrabold text-gray-900 dark:text-white tracking-tight">Pengaturan Profil</h1>
            <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium text-lg">Kelola informasi kontak dan keamanan akun Anda</p>
        </div>

        <div class="space-y-10">
            <!-- Contact Info Section -->
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl shadow-blue-500/5 border border-gray-100 dark:border-gray-800 p-8 sm:p-10 transition-all">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Informasi Kontak</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Data ini digunakan untuk komunikasi jasa</p>
                    </div>
                </div>

                @if (session()->has('contact_success'))
                    <div class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 animate-in fade-in slide-in-from-top-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('contact_success') }}</span>
                    </div>
                @endif

                <form wire:submit="updateContact" class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Name -->
                        <div class="md:col-span-2">
                            <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Nama Lengkap</label>
                            <input 
                                type="text" 
                                id="name"
                                wire:model="name"
                                class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('name') ? 'border-red-500' : '' }}"
                            >
                            @error('name')
                                <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Email</label>
                            <input 
                                type="email" 
                                id="email"
                                wire:model="email"
                                class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('email') ? 'border-red-500' : '' }}"
                            >
                            @error('email')
                                <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div>
                            <label for="phone" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Nomor Telepon</label>
                            <input 
                                type="tel" 
                                id="phone"
                                wire:model="phone"
                                class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('phone') ? 'border-red-500' : '' }}"
                            >
                            @error('phone')
                                <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Address -->
                        <div class="md:col-span-2">
                            <label for="address" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Alamat</label>
                            <textarea 
                                id="address"
                                wire:model="address"
                                rows="3"
                                class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('address') ? 'border-red-500' : '' }}"
                                placeholder="Alamat lengkap Anda..."
                            ></textarea>
                            @error('address')
                                <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button 
                            type="submit"
                            class="px-8 py-4 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-blue-500/25 active:scale-[0.98] flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="updateContact">Simpan Perubahan</span>
                            <span wire:loading wire:target="updateContact" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Password Section -->
            <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-2xl shadow-blue-500/5 border border-gray-100 dark:border-gray-800 p-8 sm:p-10 transition-all">
                <div class="flex items-center gap-4 mb-8">
                    <div class="w-12 h-12 bg-amber-100 dark:bg-amber-900/30 rounded-2xl flex items-center justify-center text-amber-600 dark:text-amber-400">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Keamanan Akun</h2>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium">Perbarui kata sandi secara berkala</p>
                    </div>
                </div>

                @if (session()->has('password_success'))
                    <div class="mb-8 p-4 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-2xl flex items-center gap-3 text-emerald-700 dark:text-emerald-400 animate-in fade-in slide-in-from-top-4">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span class="text-sm font-bold">{{ session('password_success') }}</span>
                    </div>
                @endif

                <form wire:submit="updatePassword" class="space-y-6">
                    <div class="space-y-6">
                        <!-- Current Password -->
                        <div>
                            <label for="current_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Kata Sandi Saat Ini</label>
                            <input 
                                type="password" 
                                id="current_password"
                                wire:model="current_password"
                                class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('current_password') ? 'border-red-500' : '' }}"
                                placeholder="••••••••"
                            >
                            @error('current_password')
                                <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- New Password -->
                            <div>
                                <label for="new_password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Kata Sandi Baru</label>
                                <input 
                                    type="password" 
                                    id="new_password"
                                    wire:model="new_password"
                                    class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('new_password') ? 'border-red-500' : '' }}"
                                    placeholder="••••••••"
                                >
                                @error('new_password')
                                    <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Confirm New Password -->
                            <div>
                                <label for="new_password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Konfirmasi Sandi Baru</label>
                                <input 
                                    type="password" 
                                    id="new_password_confirmation"
                                    wire:model="new_password_confirmation"
                                    class="w-full px-5 py-4 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white"
                                    placeholder="••••••••"
                                >
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end pt-4">
                        <button 
                            type="submit"
                            class="px-8 py-4 bg-amber-600 hover:bg-amber-700 text-white font-bold rounded-2xl transition-all shadow-lg shadow-amber-500/25 active:scale-[0.98] flex items-center gap-2"
                        >
                            <span wire:loading.remove wire:target="updatePassword">Ubah Kata Sandi</span>
                            <span wire:loading wire:target="updatePassword" class="flex items-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Mengubah...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

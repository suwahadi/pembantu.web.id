<div class="min-h-[90vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-xl space-y-8">
        <!-- Card -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl shadow-blue-500/10 border border-gray-100 dark:border-gray-800 p-8 sm:p-10 transition-all">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <x-icon.logo class="text-white h-8 w-8" />
                    </div>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Buat Akun Baru</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Bergabung dengan platform jasa terpercaya</p>
            </div>

            <!-- Form -->
            <form wire:submit="submit" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Name -->
                    <div class="md:col-span-2">
                        <label for="name" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Nama Lengkap</label>
                        <input 
                            type="text" 
                            id="name"
                            wire:model="name"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('name') ? 'border-red-500 ring-red-500/20' : '' }}"
                            placeholder="Nama Lengkap Anda"
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
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('email') ? 'border-red-500 ring-red-500/20' : '' }}"
                            placeholder="nama@email.com"
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
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('phone') ? 'border-red-500 ring-red-500/20' : '' }}"
                            placeholder="08xxxxxxxxxx"
                        >
                        @error('phone')
                            <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Role Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3 ml-1">Daftar Sebagai</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <label class="relative flex flex-col p-4 border rounded-2xl cursor-pointer transition-all {{ $role === 'visitor' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/10 ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30' }}" wire:click="$set('role', 'visitor')">
                                <span class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-gray-900 dark:text-white">Pencari Jasa</span>
                                    <input type="radio" name="role" value="visitor" wire:model="role" class="w-4 h-4 text-blue-600 focus:ring-blue-500/20">
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Cari tenaga kerja profesional</span>
                            </label>
                            
                            <label class="relative flex flex-col p-4 border rounded-2xl cursor-pointer transition-all {{ $role === 'agency' ? 'border-blue-500 bg-blue-50/50 dark:bg-blue-900/10 ring-2 ring-blue-500/20' : 'border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/30' }}" wire:click="$set('role', 'agency')">
                                <span class="flex items-center justify-between mb-1">
                                    <span class="font-bold text-gray-900 dark:text-white">Agensi</span>
                                    <input type="radio" name="role" value="agency" wire:model="role" class="w-4 h-4 text-blue-600 focus:ring-blue-500/20">
                                </span>
                                <span class="text-xs text-gray-500 dark:text-gray-400">Daftarkan tim tenaga kerja Anda</span>
                            </label>
                        </div>
                        @error('role')
                            <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Kata Sandi</label>
                        <input 
                            type="password" 
                            id="password"
                            wire:model="password"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('password') ? 'border-red-500 ring-red-500/20' : '' }}"
                            placeholder="••••••••"
                        >
                        @error('password')
                            <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Konfirmasi Sandi</label>
                        <input 
                            type="password" 
                            id="password_confirmation"
                            wire:model="password_confirmation"
                            class="w-full px-4 py-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white"
                            placeholder="••••••••"
                        >
                    </div>
                </div>

                <!-- Terms -->
                <div class="flex items-start space-x-3 ml-1 pt-2">
                    <input type="checkbox" id="terms" wire:model="terms" class="mt-1 w-5 h-5 border-gray-300 dark:border-gray-700 rounded-lg text-blue-600 focus:ring-blue-500/20 transition-all cursor-pointer">
                    <label for="terms" class="text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer italic">
                        Saya menerima <a href="#" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">Syarat & Ketentuan</a> serta <a href="#" class="text-blue-600 dark:text-blue-400 font-bold hover:underline">Kebijakan Privasi</a> yang berlaku.
                    </label>
                </div>
                @error('terms')
                    <p class="text-red-500 text-xs mt-1 ml-1 font-medium italic">{{ $message }}</p>
                @enderror

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-2xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg shadow-blue-500/25 active:scale-[0.98]"
                >
                    <span wire:loading.remove wire:target="submit">Daftar Sekarang</span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>

            <!-- Login Link -->
            <div class="mt-8 text-center border-t border-gray-100 dark:border-gray-800 pt-8">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Sudah punya akun? 
                    <a href="/login" class="text-blue-600 dark:text-blue-400 font-bold hover:underline ml-1">Masuk Sekarang</a>
                </p>
            </div>
        </div>

        <!-- Footer Info -->
        <p class="text-center text-xs text-gray-500 dark:text-gray-500 pb-8">
            &copy; {{ date('Y') }} Pembantu.web.id. Seluruh hak cipta dilindungi.
        </p>
    </div>
</div>

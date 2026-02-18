<div class="min-h-[80vh] flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md space-y-8">
        <!-- Card -->
        <div class="bg-white dark:bg-gray-900 rounded-3xl shadow-2xl shadow-blue-500/10 border border-gray-100 dark:border-gray-800 p-8 sm:p-10 transition-all">
            <!-- Header -->
            <div class="text-center mb-10">
                <div class="flex justify-center mb-6">
                    <div class="w-16 h-16 bg-gradient-to-tr from-blue-600 to-blue-400 rounded-2xl flex items-center justify-center shadow-lg shadow-blue-500/30">
                        <x-icon.logo class="text-white h-8 w-8" />
                    </div>
                </div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">Selamat Datang</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-2 font-medium">Masuk untuk melanjutkan ke Pembantu.web.id</p>
            </div>

            <!-- Form -->
            <form wire:submit="submit" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2 ml-1">Email</label>
                    <div class="relative">
                        <input 
                            type="email" 
                            id="email"
                            wire:model="email"
                            class="w-full pl-4 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('email') ? 'border-red-500 ring-red-500/20' : '' }}"
                            placeholder="nama@email.com"
                        >
                    </div>
                    @error('email')
                        <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <div class="flex justify-between items-center mb-2 ml-1">
                        <label for="password" class="text-sm font-semibold text-gray-700 dark:text-gray-300">Kata Sandi</label>
                        <a href="/forgot-password" class="text-xs font-semibold text-blue-600 hover:text-blue-500 transition-colors">Lupa Password?</a>
                    </div>
                    <div class="relative">
                        <input 
                            type="password" 
                            id="password"
                            wire:model="password"
                            class="w-full pl-4 pr-4 py-3 bg-gray-50 dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700 rounded-2xl focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition-all dark:text-white {{ $errors->has('password') ? 'border-red-500 ring-red-500/20' : '' }}"
                            placeholder="••••••••"
                        >
                    </div>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1.5 ml-1 font-medium italic">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center space-x-3 ml-1">
                    <input type="checkbox" id="remember" wire:model="remember" class="w-5 h-5 border-gray-300 dark:border-gray-700 rounded-lg text-blue-600 focus:ring-blue-500/20 transition-all cursor-pointer">
                    <label for="remember" class="text-sm font-medium text-gray-600 dark:text-gray-400 cursor-pointer">Ingat saya</label>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-2xl text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-lg shadow-blue-500/25 active:scale-[0.98]"
                >
                    <span wire:loading.remove wire:target="submit">Masuk ke Akun</span>
                    <span wire:loading wire:target="submit" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Memproses...
                    </span>
                </button>
            </form>

            <!-- Sign Up Link -->
            <div class="mt-8 text-center">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                    Belum punya akun? 
                    <a href="/register" class="text-blue-600 dark:text-blue-400 font-bold hover:underline ml-1">Daftar Sekarang</a>
                </p>
            </div>
        </div>

        <!-- Footer Info -->
        <p class="text-center text-xs text-gray-500 dark:text-gray-500">
            &copy; {{ date('Y') }} Pembantu.web.id. Seluruh hak cipta dilindungi.
        </p>
    </div>
</div>

<div class="min-h-screen bg-gradient-to-br from-blue-50 to-blue-100 flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div class="w-full max-w-md">
        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="flex justify-center mb-4">
                    <div class="w-12 h-12 bg-blue-600 rounded-lg flex items-center justify-center">
                        <span class="text-white font-bold text-lg">P</span>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-gray-900">Masuk</h1>
                <p class="text-gray-600 mt-2">Akses akun Pembantu Anda</p>
            </div>

            <!-- Form -->
            <form wire:submit="submit" class="space-y-6">
                <!-- Email -->
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-900 mb-2">Email</label>
                    <input 
                        type="email" 
                        id="email"
                        wire:model="email"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('email') ? 'border-red-500' : '' }}"
                        placeholder="name@example.com"
                    >
                    @error('email')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-900 mb-2">Kata Sandi</label>
                    <input 
                        type="password" 
                        id="password"
                        wire:model="password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('password') ? 'border-red-500' : '' }}"
                        placeholder="••••••••"
                    >
                    @error('password')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember & Forgot -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center space-x-2">
                        <input type="checkbox" wire:model="remember" class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-blue-500">
                        <span class="text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="/forgot-password" class="text-sm text-blue-600 hover:text-blue-800">Lupa kata sandi?</a>
                </div>

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Masuk
                </button>
            </form>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-gray-300"></div>
                </div>
                <div class="relative flex justify-center text-sm">
                    <span class="px-2 bg-white text-gray-500">atau</span>
                </div>
            </div>

            <!-- Sign Up Link -->
            <p class="text-center text-gray-600">
                Belum punya akun? 
                <a href="/register" class="text-blue-600 font-semibold hover:text-blue-800">Daftar sekarang</a>
            </p>
        </div>

        <!-- Additional Info -->
        <div class="mt-8 text-center text-sm text-gray-600">
            <p>Dengan masuk, Anda menerima <a href="#" class="text-blue-600 hover:text-blue-800">Syarat & Ketentuan</a> kami</p>
        </div>
    </div>
</div>

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
                <h1 class="text-3xl font-bold text-gray-900">Daftar</h1>
                <p class="text-gray-600 mt-2">Bergabung dengan Pembantu.web.id</p>
            </div>

            <!-- Form -->
            <form wire:submit="submit" class="space-y-5">
                <!-- Name -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-900 mb-2">Nama Lengkap</label>
                    <input 
                        type="text" 
                        id="name"
                        wire:model="name"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('name') ? 'border-red-500' : '' }}"
                        placeholder="Nama Anda"
                    >
                    @error('name')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

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

                <!-- Phone -->
                <div>
                    <label for="phone" class="block text-sm font-medium text-gray-900 mb-2">Nomor Telepon</label>
                    <input 
                        type="tel" 
                        id="phone"
                        wire:model="phone"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $errors->has('phone') ? 'border-red-500' : '' }}"
                        placeholder="08XXXXXXXXXX"
                    >
                    @error('phone')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-900 mb-2">Daftar Sebagai</label>
                    <div class="space-y-2">
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50" wire:click="$set('role', 'visitor')">
                            <input type="radio" name="role" value="visitor" wire:model="role" class="w-4 h-4 text-blue-600">
                            <span class="ml-3">
                                <span class="font-medium text-gray-900">Pencari Jasa</span>
                                <p class="text-sm text-gray-500">Cari tenaga kerja profesional</p>
                            </span>
                        </label>
                        <label class="flex items-center p-3 border border-gray-300 rounded-lg cursor-pointer hover:bg-blue-50" wire:click="$set('role', 'agency')">
                            <input type="radio" name="role" value="agency" wire:model="role" class="w-4 h-4 text-blue-600">
                            <span class="ml-3">
                                <span class="font-medium text-gray-900">Agensi</span>
                                <p class="text-sm text-gray-500">Daftarkan tim tenaga kerja Anda</p>
                            </span>
                        </label>
                    </div>
                    @error('role')
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

                <!-- Password Confirmation -->
                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-900 mb-2">Konfirmasi Kata Sandi</label>
                    <input 
                        type="password" 
                        id="password_confirmation"
                        wire:model="password_confirmation"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="••••••••"
                    >
                </div>

                <!-- Terms -->
                <label class="flex items-start space-x-2">
                    <input type="checkbox" wire:model="terms" class="w-4 h-4 border-gray-300 rounded focus:ring-2 focus:ring-blue-500 mt-1">
                    <span class="text-sm text-gray-600">
                        Saya menerima <a href="#" class="text-blue-600 hover:text-blue-800">Syarat & Ketentuan</a> dan <a href="#" class="text-blue-600 hover:text-blue-800">Kebijakan Privasi</a>
                    </span>
                </label>
                @error('terms')
                    <p class="text-red-500 text-sm">{{ $message }}</p>
                @enderror

                <!-- Submit Button -->
                <button 
                    type="submit"
                    class="w-full py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    Daftar
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

            <!-- Login Link -->
            <p class="text-center text-gray-600">
                Sudah punya akun? 
                <a href="/login" class="text-blue-600 font-semibold hover:text-blue-800">Masuk sekarang</a>
            </p>
        </div>
    </div>
</div>

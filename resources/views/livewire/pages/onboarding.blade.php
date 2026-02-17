<div class="min-h-screen bg-gradient-to-br from-blue-50 to-white py-12 px-4">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-2">Selamat Datang di Pembantu!</h1>
            <p class="text-lg text-gray-600">Mari kita siapkan profil Anda dalam beberapa langkah sederhana</p>
        </div>

        <!-- Progress Bar -->
        <div class="mb-12">
            <div class="flex justify-between mb-4">
                @for ($i = 1; $i <= 4; $i++)
                    <div class="flex flex-col items-center">
                        <div class="w-12 h-12 rounded-full flex items-center justify-center font-semibold text-lg {{ $i <= $step ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-600' }}">
                            {{ $i }}
                        </div>
                        <p class="text-xs mt-2 text-gray-600">
                            @switch($i)
                                @case(1)
                                    Profil
                                @break
                                @case(2)
                                    Verifikasi
                                @break
                                @case(3)
                                    Dokumen
                                @break
                                @case(4)
                                    Selesai
                                @break
                            @endswitch
                        </p>
                    </div>
                @endfor
            </div>
            <div class="h-1 bg-gray-200 rounded">
                <div class="h-full bg-blue-600 rounded transition-all duration-300" style="width: {{ ($step / 4) * 100 }}%"></div>
            </div>
        </div>

        <!-- Content Card -->
        <div class="bg-white rounded-lg shadow-lg p-8 mb-8">
            @if ($step == 1)
                <!-- Step 1: Profile -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">{{ auth()->user()->name }}</h2>
                    <p class="text-gray-600 mb-6">Lengkapi informasi profil Anda untuk memulai</p>
                    
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                            <input type="email" value="{{ auth()->user()->email }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Nomor Telepon</label>
                            <input type="tel" value="{{ auth()->user()->phone }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Peran</label>
                            <input type="text" value="{{ ucfirst($role) }}" disabled class="w-full px-4 py-2 border border-gray-300 rounded-lg bg-gray-50 text-gray-600">
                        </div>
                    </div>
                </div>
            @elseif ($step == 2)
                <!-- Step 2: Verification -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Verifikasi Identitas</h2>
                    <p class="text-gray-600 mb-6">Kami memerlukan verifikasi identitas Anda untuk keamanan</p>
                    
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-4">
                        <p class="text-blue-800 text-sm">✓ Email Anda telah terverifikasi</p>
                    </div>
                    
                    <div class="space-y-3">
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" checked class="w-4 h-4 text-blue-600 rounded">
                            <span class="ml-3 text-gray-700">Nomor identitas terverifikasi</span>
                        </label>
                        <label class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer">
                            <input type="checkbox" class="w-4 h-4 text-blue-600 rounded">
                            <span class="ml-3 text-gray-700">Nomor rekening bank terverifikasi</span>
                        </label>
                    </div>
                </div>
            @elseif ($step == 3)
                <!-- Step 3: Documents -->
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-900 mb-4">Unggah Dokumen</h2>
                    <p class="text-gray-600 mb-6">Unggah dokumen pendukung untuk profil Anda</p>
                    
                    <div class="space-y-4">
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition cursor-pointer">
                            <div class="w-12 h-12 mx-auto mb-3 flex items-center justify-center bg-gray-100 rounded-lg">
                                📄
                            </div>
                            <p class="text-gray-700 font-medium">Klik untuk unggah KTP</p>
                            <p class="text-xs text-gray-500">Atau seret file ke sini</p>
                        </div>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-blue-400 transition cursor-pointer">
                            <div class="w-12 h-12 mx-auto mb-3 flex items-center justify-center bg-gray-100 rounded-lg">
                                📄
                            </div>
                            <p class="text-gray-700 font-medium">Klik untuk unggah NPWP (opsional)</p>
                            <p class="text-xs text-gray-500">Atau seret file ke sini</p>
                        </div>
                    </div>
                </div>
            @else
                <!-- Step 4: Complete -->
                <div class="text-center mb-6">
                    <div class="w-16 h-16 mx-auto mb-4 bg-green-100 rounded-full flex items-center justify-center">
                        <span class="text-3xl">✓</span>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 mb-2">Profil Siap!</h2>
                    <p class="text-gray-600">Profil Anda telah berhasil dibuat. Mari mulai menggunakan Pembantu!</p>
                </div>
            @endif
        </div>

        <!-- Navigation Buttons -->
        <div class="flex justify-between gap-4">
            <button 
                wire:click="prevStep"
                {{ $step == 1 ? 'disabled' : '' }}
                class="flex-1 px-6 py-3 border border-gray-300 text-gray-700 font-semibold rounded-lg hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed"
            >
                ← Kembali
            </button>
            <button 
                wire:click="{{ $step == 4 ? 'completeOnboarding' : 'nextStep' }}"
                class="flex-1 px-6 py-3 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 transition"
            >
                {{ $step == 4 ? 'Mulai Menggunakan' : 'Lanjutkan →' }}
            </button>
        </div>
    </div>
</div>

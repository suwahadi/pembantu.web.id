<div class="max-w-3xl mx-auto space-y-4">
  @if (session('error'))
    <x-card><div class="text-sm text-red-700">{{ session('error') }}</div></x-card>
  @endif

  <x-card :title="$workerId ? 'Edit Worker' : 'Tambah Worker'" subtitle="Lengkapi data tenaga kerja untuk ditampilkan di katalog.">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
      <x-form.input label="Nama" wire:model.live="name" />
      <x-form.select label="Kategori" wire:model.live="categoryId" :options="$categories" />

      <x-form.select label="Lokasi (opsional)" wire:model.live="locationId" :options="['' => '- Pilih -'] + $locations" />
      <x-form.select label="Skema Default" wire:model.live="defaultScheme"
        :options="['HARIAN' => 'Harian', 'MINGGUAN' => 'Mingguan', 'BULANAN' => 'Bulanan', 'PER_JAM' => 'Per Jam']" />

      <x-form.input label="Harga Minimal (IDR)" type="number" wire:model.live="minPriceIdr" />
    </div>

    <div class="mt-3 grid grid-cols-1 gap-3">
      <x-form.textarea label="Bio/Deskripsi" wire:model.live="bio" :rows="3" />
      <x-form.textarea label="Skills (pisahkan dengan koma)" wire:model.live="skills" :rows="2" />
    </div>

    <div class="mt-4 border-t pt-4 space-y-2">
      <div class="text-sm font-medium">Foto (opsional)</div>

      @if($existingPhotoPath)
        <div class="flex items-center gap-3">
          <img src="{{ asset('storage/' . $existingPhotoPath) }}" class="w-20 h-20 rounded-xl object-cover border" />
          <x-button size="sm" variant="danger" wire:click="removePhoto" wire:loading.attr="disabled">
            Hapus Foto
          </x-button>
        </div>
      @endif

      <input type="file" wire:model="photo" class="block w-full text-sm" />
      @error('photo')
        <div class="text-xs text-red-600">{{ $message }}</div>
      @enderror
      <div class="text-xs text-gray-600">Maks 4MB, format jpg/png/webp.</div>
    </div>

    <div class="mt-4 flex justify-between">
      <a class="underline text-sm" href="{{ route('agency.workers.index') }}">Kembali</a>
      <x-button wire:click="save" wire:loading.attr="disabled">
        <span wire:loading.remove>Simpan</span>
        <span wire:loading>Menyimpan...</span>
      </x-button>
    </div>
  </x-card>
</div>

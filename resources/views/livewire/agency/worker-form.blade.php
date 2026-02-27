<div class="max-w-3xl mx-auto space-y-4">
 @if (session('error'))
    <x-card><div class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div></x-card>
 @endif

  <x-card :title="$workerId ? 'Edit Worker' : 'Tambah Worker'" subtitle="Lengkapi data tenaga kerja untuk ditampilkan di katalog.">
    <form wire:submit.prevent="save" enctype="multipart/form-data">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
        <x-form.input label="Nama" wire:model.live="name" />
        <x-form.select label="Kategori" wire:model.live="categoryId" :options="$categories" />
      </div>

      <div class="mt-3 grid grid-cols-1 gap-3">
        <x-form.textarea label="Bio/Deskripsi" wire:model.live="bio" :rows="3" />
      </div>

      <!-- Skills Section -->
      <div class="mt-4 border-t dark:border-gray-700 pt-4">
        <div class="text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Keahlian (Skills)</div>
        <div class="flex flex-wrap gap-2">
          @foreach($allSkills as $skill)
            <label class="cursor-pointer">
              <input type="checkbox" wire:model.live="skillIds" value="{{ $skill->id }}" class="hidden peer" />
              <div class="px-3 py-1 rounded-full border border-gray-300 dark:border-gray-600 text-xs text-gray-700 dark:text-gray-300 transition peer-checked:bg-blue-600 peer-checked:text-white peer-checked:border-blue-600 hover:bg-gray-50 dark:hover:bg-gray-700">
                {{ $skill->name }}
              </div>
            </label>
          @endforeach
        </div>
        @error('skillIds') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <!-- Service Areas Section -->
      <div class="mt-6 border-t dark:border-gray-700 pt-4">
        <div class="text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Wilayah Layanan (Area)</div>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2 h-40 overflow-y-auto p-2 border rounded-xl dark:border-gray-700">
          @foreach($locations as $locId => $locCity)
            <label class="flex items-center gap-2 cursor-pointer p-1 hover:bg-gray-50 dark:hover:bg-gray-800 rounded">
              <input type="checkbox" wire:model.live="serviceAreaIds" value="{{ $locId }}" class="rounded border-gray-300 dark:border-gray-600 text-blue-600 dark:text-blue-400 focus:ring-blue-500" />
              <span class="text-xs text-gray-700 dark:text-gray-300">{{ $locCity }}</span>
            </label>
          @endforeach
        </div>
        <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-1">* Worker akan muncul di pencarian untuk wilayah yang dipilih.</div>
        @error('serviceAreaIds') <div class="text-xs text-red-600 mt-1">{{ $message }}</div> @enderror
      </div>

      <!-- Pricing Section -->
      <div class="mt-6 border-t dark:border-gray-700 pt-4">
        <div class="text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Daftar Harga Layanan</div>
        <div class="space-y-3">
          @foreach($pricings as $index => $p)
            <div class="flex flex-wrap md:flex-nowrap gap-3 items-end bg-gray-50 dark:bg-gray-800 p-3 rounded-xl border dark:border-gray-700">
              <div class="w-full md:w-1/3">
                <x-form.select label="Tipe" wire:model.live="pricings.{{ $index }}.pricing_type"
                  :options="['hourly' => 'Per Jam', 'daily' => 'Harian', 'weekly' => 'Mingguan', 'monthly' => 'Bulanan', 'project' => 'Borongan']" />
              </div>
              <div class="w-full md:w-1/3">
                <x-form.input label="Harga (IDR)" type="number" wire:model.live="pricings.{{ $index }}.price_idr" />
              </div>
              <div class="w-full md:w-1/3 flex gap-2">
                <x-form.input label="Keterangan" wire:model.live="pricings.{{ $index }}.description" placeholder="Optional" />
                @if(count($pricings) > 1)
                  <button type="button" wire:click="removePricing({{ $index }})" class="p-2 text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900 rounded-lg">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                  </button>
                @endif
              </div>
            </div>
          @endforeach
          <button type="button" wire:click="addPricing" class="text-sm text-blue-600 dark:text-blue-400 font-medium hover:underline">+ Tambah Tipe Harga</button>
        </div>
        <div class="text-[10px] text-gray-500 dark:text-gray-400 mt-2">* Gunakan biaya minimal sebagai harga default yang tampil di katalog.</div>
      </div>

      <div class="mt-4 border-t dark:border-gray-700 pt-4 space-y-2">
        <div class="text-sm font-medium text-gray-700 dark:text-gray-300">Foto (opsional)</div>

        @if($existingPhotoPath)
          <div class="flex items-center gap-3">
            <img src="{{ asset('storage/' . $existingPhotoPath) }}" class="w-20 h-20 rounded-xl object-cover border dark:border-gray-600" />
            <x-button size="sm" variant="danger" wire:click="removePhoto" wire:loading.attr="disabled">
              Hapus Foto
            </x-button>
          </div>
        @endif

        @if($photoPath)
          <div class="flex items-center gap-3">
            <img src="{{ asset('storage/' . $photoPath) }}" class="w-20 h-20 rounded-xl object-cover border dark:border-gray-600" />
            <button type="button" onclick="removeNewPhoto()" class="px-3 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700">
              Hapus
            </button>
          </div>
        @endif

        <div class="space-y-2">
          <input type="file" 
                 id="photo-upload"
                 accept="image/jpeg,image/jpg,image/png,image/webp" 
                 class="block w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 dark:file:bg-blue-900/30 dark:file:text-blue-300" />
          <div class="text-xs text-gray-600 dark:text-gray-400">Maks 4MB, format jpg/png/webp.</div>
        </div>
      </div>

      <div class="mt-4 flex justify-between">
        <a href="{{ route('agency.workers.index') }}" class="px-4 py-2 rounded-lg text-sm font-medium border border-primary-600 bg-gray-900 text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:border-gray-300 dark:hover:bg-gray-100 transition-colors">Kembali</a>
        <x-button type="submit" wire:loading.attr="disabled">
          <span wire:loading.remove>Simpan</span>
          <span wire:loading>Menyimpan...</span>
        </x-button>
      </div>
    </form>
  </x-card>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo-upload');
    
    photoInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            uploadPhoto(file);
        }
    });
});

function uploadPhoto(file) {
    const formData = new FormData();
    formData.append('photo', file);
    
    // Show loading
    const input = document.getElementById('photo-upload');
    const originalText = input.nextElementSibling.textContent;
    input.nextElementSibling.textContent = 'Mengupload...';
    input.disabled = true;
    
    fetch('{{ route("agency.workers.upload-photo") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update Livewire property
            @this.set('photoPath', data.path);
            
            // Clear input
            input.value = '';
            
            // Show success
            input.nextElementSibling.textContent = 'Photo berhasil diupload!';
            setTimeout(() => {
                input.nextElementSibling.textContent = originalText;
            }, 2000);
        } else {
            alert('Upload failed: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Upload error:', error);
        alert('Upload failed. Please try again.');
    })
    .finally(() => {
        input.disabled = false;
    });
}

function removeNewPhoto() {
    const photoPath = @this.get('photoPath');
    
    if (photoPath) {
        fetch('{{ route("agency.workers.remove-photo") }}', {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                path: photoPath
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear Livewire property
                @this.set('photoPath', null);
            } else {
                alert('Failed to remove photo: ' + data.error);
            }
        })
        .catch(error => {
            console.error('Remove error:', error);
            alert('Failed to remove photo. Please try again.');
        });
    }
}
</script>

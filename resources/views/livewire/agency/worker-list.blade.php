<div class="space-y-4">
  @if (session('success'))
    <x-card><div class="text-sm text-green-700 dark:text-green-300">{{ session('success') }}</div></x-card>
  @endif
  @if (session('error'))
    <x-card><div class="text-sm text-red-700 dark:text-red-300">{{ session('error') }}</div></x-card>
  @endif

  <x-card title="Workers" subtitle="Kelola tenaga kerja milik agency.">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <x-form.input label="Cari" wire:model.live="q" placeholder="Nama / skill" />
      <x-form.select label="Kategori" wire:model.live="categoryId" :options="['' => '- Semua -'] + $categories" />
      <x-form.select label="Lokasi" wire:model.live="locationId" :options="['' => '- Semua -'] + $locations" />
      <div class="flex items-end justify-end">
        <a href="{{ route('agency.workers.create') }}" class="underline text-sm text-blue-600 dark:text-blue-400">+ Tambah Worker</a>
      </div>
    </div>

    <div class="mt-4 overflow-auto border rounded-xl dark:border-gray-700">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th class="text-left p-3 text-gray-700 dark:text-gray-300">Nama</th>
            <th class="text-left p-3 text-gray-700 dark:text-gray-300">Kategori</th>
            <th class="text-left p-3 text-gray-700 dark:text-gray-300">Lokasi</th>
            <th class="text-left p-3 text-gray-700 dark:text-gray-300">Skema</th>
            <th class="text-left p-3 text-gray-700 dark:text-gray-300">Harga Min</th>
            <th class="text-left p-3 text-gray-700 dark:text-gray-300">Status</th>
            <th class="p-3 text-gray-700 dark:text-gray-300"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $w)
            <tr class="border-t dark:border-gray-700">
              <td class="p-3 font-medium">
                <div class="flex items-center gap-3">
                  @if($w->photo_path)
                    <img src="{{ asset('storage/' . $w->photo_path) }}" class="w-10 h-10 rounded-lg object-cover border" />
                  @else
                    <div class="w-10 h-10 rounded-lg bg-gray-100 dark:bg-gray-800 border dark:border-gray-600"></div>
                  @endif
                  <div>
                    <div class="text-gray-900 dark:text-white">{{ $w->name }}</div>
                    <div class="text-xs text-gray-600 dark:text-gray-400">{{ $w->location_name }}</div>
                  </div>
                </div>
              </td>
              <td class="p-3 text-gray-900 dark:text-white">{{ $w->category_name }}</td>
              <td class="p-3 text-gray-900 dark:text-white">{{ $w->location_name }}</td>
              <td class="p-3 text-gray-900 dark:text-white">{{ $w->default_scheme }}</td>
              <td class="p-3 text-gray-900 dark:text-white">Rp {{ number_format($w->min_price, 0, ',', '.') }}</td>
              <td class="p-3">
                <button type="button"
                  wire:click="toggleActive({{ $w->id }})"
                  wire:loading.attr="disabled"
                  class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 {{ $w->is_active ? 'bg-green-600' : 'bg-gray-200 dark:bg-gray-700' }}">
                  <span class="inline-block h-4 w-4 transform rounded-full bg-white transition-transform {{ $w->is_active ? 'translate-x-6' : 'translate-x-1' }}"></span>
                </button>
              </td>
              <td class="p-3 text-right whitespace-nowrap">
                <a class="underline text-sm text-blue-600 dark:text-blue-400" href="{{ route('agency.workers.edit', $w->id) }}">Edit</a>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $items->links() }}</div>
  </x-card>
</div>

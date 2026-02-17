<div class="space-y-4">
  @if (session('success'))
    <x-card><div class="text-sm text-green-700">{{ session('success') }}</div></x-card>
  @endif
  @if (session('error'))
    <x-card><div class="text-sm text-red-700">{{ session('error') }}</div></x-card>
  @endif

  <x-card title="Workers" subtitle="Kelola tenaga kerja milik agency.">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-3">
      <x-form.input label="Cari" wire:model.live="q" placeholder="Nama / skill" />
      <x-form.select label="Kategori" wire:model.live="categoryId" :options="['' => '- Semua -'] + $categories" />
      <x-form.select label="Lokasi" wire:model.live="locationId" :options="['' => '- Semua -'] + $locations" />
      <div class="flex items-end justify-end">
        <a href="{{ route('agency.workers.create') }}" class="underline text-sm">+ Tambah Worker</a>
      </div>
    </div>

    <div class="mt-4 overflow-auto border rounded-xl">
      <table class="w-full text-sm">
        <thead class="bg-gray-50">
          <tr>
            <th class="text-left p-3">Nama</th>
            <th class="text-left p-3">Kategori</th>
            <th class="text-left p-3">Lokasi</th>
            <th class="text-left p-3">Skema</th>
            <th class="text-left p-3">Harga Min</th>
            <th class="text-left p-3">Status</th>
            <th class="p-3"></th>
          </tr>
        </thead>
        <tbody>
          @foreach($items as $w)
            <tr class="border-t">
              <td class="p-3 font-medium">
                <div class="flex items-center gap-3">
                  @if($w->photo_path)
                    <img src="{{ asset('storage/' . $w->photo_path) }}" class="w-10 h-10 rounded-lg object-cover border" />
                  @else
                    <div class="w-10 h-10 rounded-lg bg-gray-100 border"></div>
                  @endif
                  <div>
                    <div>{{ $w->name }}</div>
                    <div class="text-xs text-gray-600">{{ $w->location_name }}</div>
                  </div>
                </div>
              </td>
              <td class="p-3">{{ $w->category_name }}</td>
              <td class="p-3">{{ $w->location_name }}</td>
              <td class="p-3">{{ $w->default_scheme }}</td>
              <td class="p-3">Rp {{ number_format($w->min_price_idr, 0, ',', '.') }}</td>
              <td class="p-3">
                @if($w->is_active)
                  <x-badge variant="green">Aktif</x-badge>
                @else
                  <x-badge variant="red">Nonaktif</x-badge>
                @endif
              </td>
              <td class="p-3 text-right whitespace-nowrap">
                <a class="underline text-sm" href="{{ route('agency.workers.edit', $w->id) }}">Edit</a>
                <span class="mx-2 text-gray-300">|</span>
                <x-button size="sm" variant="secondary"
                  wire:click="toggleActive({{ $w->id }})"
                  wire:loading.attr="disabled">
                  Toggle
                </x-button>
              </td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </div>

    <div class="mt-3">{{ $items->links() }}</div>
  </x-card>
</div>

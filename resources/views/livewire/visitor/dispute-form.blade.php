<div class="w-full {{ $embedded ? '' : 'max-w-2xl mx-auto px-4 sm:px-6 lg:px-8' }}">
    <x-card title="Ajukan Dispute" class="rounded-2xl shadow-sm">
        <p class="mb-4 text-sm text-gray-600 dark:text-gray-300">
            Jelaskan masalah yang Anda hadapi dengan detail dan lampirkan bukti jika ada.
        </p>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700 dark:border-green-800 dark:bg-green-900/30 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700 dark:border-red-800 dark:bg-red-900/30 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="space-y-4">
            <div>
                <x-form.select label="Kategori Masalah" wire:model.live="category" :options="$categories" />
                <x-form.error for="category" />
            </div>

            <div>
                <x-form.textarea
                    label="Deskripsi Detail"
                    wire:model.live="description"
                    :rows="5"
                    placeholder="Jelaskan apa yang terjadi, kapan, dan dampaknya..."
                />
                <x-form.error for="description" />
            </div>

            <div>
                <label class="mb-1 block text-sm font-medium text-gray-700 dark:text-gray-300">Bukti (Opsional)</label>
                <input type="file" wire:model="evidence" class="block w-full rounded-lg border border-gray-300 p-2 text-sm text-gray-700 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-200" />
                @if ($evidence)
                    <p class="mt-1 flex items-center gap-1 text-xs text-green-600 dark:text-green-400">
                      @include('svgs.icon-check', ['class' => 'w-4 h-4 text-green-600 dark:text-green-400'])
                      File dipilih: {{ $evidence->getClientOriginalName() }}
                    </p>
                @endif
                <x-form.error for="evidence" />
            </div>

            <div class="flex flex-col-reverse gap-2 pt-2 sm:flex-row sm:justify-end">
                @if ($embedded)
                    <a href="{{ route('orders.show', $orderId) }}" class="inline-flex items-center justify-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 transition-colors hover:bg-gray-50 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-800">
                        Batal
                    </a>
                @else
                    <x-button variant="secondary" onclick="window.history.back()">Batal</x-button>
                @endif
                <x-button wire:click="submit" wire:loading.attr="disabled" class="justify-center">
                    <span wire:loading.remove>Kirim Dispute</span>
                    <span wire:loading>Mengirim...</span>
                </x-button>
            </div>
        </div>
    </x-card>
</div>

<div class="max-w-2xl mx-auto">
    <x-card title="Ajukan Dispute">
        <p class="text-sm text-gray-600 mb-4">
            Jelaskan masalah yang Anda hadapi dengan detail dan lampirkan bukti jika ada.
        </p>

        <div class="space-y-4">
            <x-form.select label="Kategori Masalah" wire:model.live="category" :options="$categories" />

            <x-form.textarea label="Deskripsi Detail" wire:model.live="description" :rows="5"
                placeholder="Jelaskan apa yang terjadi, kapan, dan dampaknya..." />

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Bukti (Opsional)</label>
                <input type="file" wire:model="evidence" class="block w-full text-sm border border-gray-300 rounded-lg p-2" />
                @if ($evidence)
                    <p class="text-xs text-green-600 mt-1">✓ File dipilih: {{ $evidence->getClientOriginalName() }}</p>
                @endif
                <x-form.error for="evidence" />
            </div>

            <div class="flex justify-between gap-2 pt-4">
                <x-button variant="secondary" onclick="window.history.back()">Batal</x-button>
                <x-button wire:click="submit">Kirim Dispute</x-button>
            </div>
        </div>
    </x-card>
</div>

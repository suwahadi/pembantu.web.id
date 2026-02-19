@props(['label' => '', 'value' => '', 'options' => [], 'error' => null])

<div class="space-y-1">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $label }}</label>
    @endif
    <select 
        class="w-full px-3 py-2 border rounded-lg text-sm bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $error ? 'border-red-500' : 'border-gray-300 dark:border-gray-600' }}"
        {{ $attributes->whereStartsWith('wire:') }}
        {{ $attributes->whereStartsWith('on') }}
    >
        <option value="">-- Pilih --</option>
        @foreach ($options as $key => $label)
            <option value="{{ $key }}" @selected((string) $value === (string) $key)>{{ $label }}</option>
        @endforeach
    </select>
    @if ($error)
        <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
    @endif
</div>

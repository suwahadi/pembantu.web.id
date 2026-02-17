@props(['label' => '', 'value' => '', 'error' => null])

<div class="space-y-1">
    @if ($label)
        <label class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    @endif
    <input 
        type="{{ $attributes->get('type', 'text') }}"
        value="{{ $value ?? old($attributes->get('name', '')) }}"
        class="w-full px-3 py-2 border rounded-lg text-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 {{ $error ? 'border-red-500' : 'border-gray-300' }}"
        {{ $attributes->whereStartsWith('wire:') }}
        {{ $attributes->whereStartsWith('on') }}
    >
    @if ($error)
        <p class="text-xs text-red-600 mt-1">{{ $error }}</p>
    @endif
</div>

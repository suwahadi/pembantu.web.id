@props(['variant' => 'primary', 'type' => 'button', 'disabled' => false])

@php
$baseClasses = 'px-4 py-2 rounded-lg font-medium text-sm transition-colors';
$variants = [
    'primary' => 'bg-blue-600 text-white hover:bg-blue-700 disabled:bg-gray-400',
    'secondary' => 'bg-gray-300 text-gray-900 hover:bg-gray-400 disabled:bg-gray-200',
    'danger' => 'bg-red-600 text-white hover:bg-red-700 disabled:bg-gray-400',
    'success' => 'bg-green-600 text-white hover:bg-green-700 disabled:bg-gray-400',
];
@endphp

<button 
    type="{{ $type }}"
    {{ $disabled ? 'disabled' : '' }}
    class="{{ $baseClasses }} {{ $variants[$variant] ?? $variants['primary'] }}"
    {{ $attributes->whereStartsWith('wire:') }}
    {{ $attributes->whereStartsWith('on') }}
>
    {{ $slot }}
</button>

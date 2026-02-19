@props(['title' => ''])

<div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 rounded-xl p-4 {{ $attributes->get('class') }}">
    @if ($title)
        <div class="font-semibold mb-3 text-gray-900 dark:text-white">{{ $title }}</div>
    @endif
    {{ $slot }}
</div>

@props(['title' => ''])

<div class="bg-white border border-gray-200 rounded-xl p-4 {{ $attributes->get('class') }}">
    @if ($title)
        <div class="font-semibold mb-3">{{ $title }}</div>
    @endif
    {{ $slot }}
</div>

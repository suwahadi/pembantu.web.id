@props(['for' => ''])

@if ($errors->has($for))
    <p class="text-xs text-red-600 mt-1">{{ $errors->first($for) }}</p>
@endif

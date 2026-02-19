@props(['events' => []])

<div class="space-y-2">
  @foreach($events as $e)
    <div class="bg-white border rounded-xl p-3">
      <div class="flex items-center justify-between">
        <div class="text-sm font-medium">{{ $e->description }}</div>
        <div class="text-xs text-gray-600">
          {{ \Carbon\Carbon::parse($e->created_at)->translatedFormat('l, d F Y H:i') }}
        </div>
      </div>
      @if(isset($e->metadata) && $e->metadata)
        <pre class="mt-2 text-xs bg-gray-50 border rounded-lg p-2 overflow-auto">{{ is_string($e->metadata) ? $e->metadata : json_encode($e->metadata, JSON_PRETTY_PRINT) }}</pre>
      @endif
    </div>
  @endforeach
</div>

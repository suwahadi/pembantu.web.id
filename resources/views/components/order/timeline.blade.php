@props(['events' => []])

<div class="space-y-2">
  @foreach($events as $e)
    <div class="bg-white border border-gray-200 rounded-xl p-3 dark:bg-gray-800 dark:border-gray-700">
      <div class="flex items-center justify-between">
        <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $e->description }}</div>
        <div class="text-xs text-gray-600 dark:text-gray-400">
          {{ \Carbon\Carbon::parse($e->created_at)->translatedFormat('l, d F Y H:i') }}
        </div>
      </div>
      @if(isset($e->metadata) && $e->metadata)
        <pre class="mt-2 text-xs bg-gray-50 border border-gray-200 rounded-lg p-2 overflow-auto dark:bg-gray-900 dark:border-gray-700 dark:text-gray-300">{{ is_string($e->metadata) ? $e->metadata : json_encode($e->metadata, JSON_PRETTY_PRINT) }}</pre>
      @endif
    </div>
  @endforeach
</div>

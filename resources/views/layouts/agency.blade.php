<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Agency - Pembantu.web.id' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="min-h-screen bg-gray-50 text-gray-900">
    <div class="min-h-screen grid grid-cols-1 lg:grid-cols-[260px_1fr]">
        <aside class="bg-white border-r">
            <div class="p-4 border-b">
                <div class="font-semibold">Pembantu.web.id</div>
                <div class="text-xs text-gray-600">Panel Agency</div>
            </div>

            @php
                $nav = [
                    ['label' => 'Dashboard', 'route' => 'agency.dashboard'],
                    ['label' => 'Kontrak', 'route' => 'agency.contracts'],
                    ['label' => 'Worker', 'route' => 'agency.workers.index'],
                    ['label' => 'Order', 'route' => 'agency.orders.index'],
                    ['label' => 'Profil', 'route' => 'profile'],
                    ['label' => 'Rekening Bank', 'route' => 'agency.bank-accounts'],
                ];
            @endphp

            <nav class="p-3 space-y-1 text-sm">
                @foreach($nav as $item)
                    @php $active = request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}"
                        class="block rounded-lg px-3 py-2 border {{ $active ? 'bg-gray-900 text-white border-gray-900' : 'bg-white border-transparent hover:bg-gray-50' }}">
                        {{ $item['label'] }}
                    </a>
                @endforeach
            </nav>

            <div class="p-4 border-t text-xs text-gray-600">
                Agency: <span class="font-medium">{{ auth()->user()->agency->name ?? '-' }}</span>
            </div>
        </aside>

        <main class="p-4 lg:p-6">
            @if (session('success'))
                <div class="mb-4 bg-green-50 border border-green-200 text-green-800 rounded-xl p-3 text-sm">
                    {{ session('success') }}
                </div>
            @endif
            @if (session('error'))
                <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-xl p-3 text-sm">
                    {{ session('error') }}
                </div>
            @endif

            {{ $slot }}
        </main>
    </div>

    @livewireScripts
</body>
</html>

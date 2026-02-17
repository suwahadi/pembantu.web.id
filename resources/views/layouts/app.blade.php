<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Pembantu.web.id' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/livewire@3/dist/livewire.js"></script>
    @livewireStyles
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col">
        <!-- Navigation -->
        <livewire:shared.navbar />
        
        <!-- Main Content -->
        <main class="flex-1">
            {{ $slot }}
        </main>
        
        <!-- Footer -->
        <livewire:shared.footer />
    </div>

    @livewireScripts
</body>
</html>

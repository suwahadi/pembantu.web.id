@extends('layouts.admin', ['title' => 'Manajemen Escrow'])

@section('content')
<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Manajemen Escrow</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Pantau dana yang ditahan (hold), dilepaskan, atau direfund untuk setiap order.</p>
            </div>
        </div>
    </div>

    <livewire:admin.escrow-hold-list />
</div>
@endsection

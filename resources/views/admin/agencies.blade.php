@extends('layouts.admin', ['title' => 'Agencies'])

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Agencies</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola semua agency dalam sistem</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.create', ['role_name' => 'agency', 'redirect' => 'admin.agencies.index']) }}" class="px-4 py-2 bg-success-500 text-white rounded-lg hover:bg-success-600 transition-colors flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Tambah Agency
                </a>
            </div>
        </div>
    </div>

    <livewire:admin.user-manager :fixed-role-name="'agency'" :lock-role-filter="true" :redirect-route="'admin.agencies.index'" />
</div>
@endsection

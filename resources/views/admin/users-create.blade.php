@extends('layouts.admin', ['title' => 'Tambah User'])

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Tambah User</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Tambah user baru ke sistem</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.users.index') }}" class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7l7 7m-7-7" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <livewire:admin.user-form :fixed-role-id="request()->integer('role') ?: null" :fixed-role-name="request()->get('role_name')" :lock-role="request()->has('role') || request()->has('role_name')" :redirect-route="request()->get('redirect')" />
</div>
@endsection

@extends('layouts.agency', ['title' => 'Tambah Order'])

@section('content')
<div>
    <!-- Page Header -->
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Tambah Order</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Buat order baru untuk customer</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('agency.orders.index') }}" class="px-4 py-2 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors flex items-center gap-2">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h14" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <!-- Form Card -->
    <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] md:p-6">
        <div class="text-center py-8">
            <svg class="h-12 w-12 text-gray-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            <p class="text-gray-500 dark:text-gray-400">Halaman tambah order dalam development</p>
            <p class="text-sm text-gray-400 dark:text-gray-500 mt-2">Fitur akan segera tersedia</p>
        </div>
    </div>
</div>
@endsection

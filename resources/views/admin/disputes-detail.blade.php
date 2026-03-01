@extends('layouts.admin', ['title' => 'Detail Dispute'])

@section('content')
<div>
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Dispute</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Tinjau keluhan, bukti, dan ambil keputusan penyelesaian</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.disputes') }}" class="px-4 py-2 rounded-lg flex items-center gap-2 border border-primary-600 bg-gray-900 text-white hover:bg-gray-800 dark:bg-white dark:text-gray-900 dark:border-gray-300 dark:hover:bg-gray-100 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <livewire:admin.dispute-detail :dispute-id="$disputeId" />
</div>
@endsection

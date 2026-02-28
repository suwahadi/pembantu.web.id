@extends('layouts.admin', ['title' => 'Disputes'])

@section('content')
<div>
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Daftar Dispute</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola dispute yang perlu ditinjau dan diselesaikan</p>
            </div>
        </div>
    </div>

    <livewire:admin.dispute-queue />
</div>
@endsection

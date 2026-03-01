@extends('layouts.admin', ['title' => 'Refunds'])

@section('content')
@php
    use App\Domain\Shared\Statuses\RefundStatus;
    use App\Models\Refund;

    $queuedCount = Refund::where('status', RefundStatus::QUEUED)->count();
    $processingCount = Refund::where('status', RefundStatus::PROCESSING)->count();
    $paidCount = Refund::where('status', RefundStatus::PAID)->count();
    $failedCount = Refund::where('status', RefundStatus::FAILED)->count();
@endphp

<div class="space-y-6">
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <p class="text-sm uppercase tracking-[0.3em] text-gray-400 dark:text-gray-500">Finance · Refund Desk</p>
            <h1 class="mt-1 text-3xl font-semibold text-gray-900 dark:text-white">Refund Management</h1>
            <p class="mt-1 text-gray-600 dark:text-gray-400">
                Monitor queued refunds, attach proofs, dan pastikan dana pelanggan dikirim tepat waktu.
            </p>
        </div>
    </div>

    <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-4">
        <x-card class="bg-white/90 dark:bg-gray-900/60">
            <p class="text-sm text-gray-500 dark:text-gray-400">Queued</p>
            <p class="mt-2 text-3xl font-bold text-amber-500 dark:text-amber-400">{{ $queuedCount }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Menunggu diproses</p>
        </x-card>
        <x-card class="bg-white/90 dark:bg-gray-900/60">
            <p class="text-sm text-gray-500 dark:text-gray-400">Processing</p>
            <p class="mt-2 text-3xl font-bold text-blue-500 dark:text-blue-400">{{ $processingCount }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Sedang ditindak</p>
        </x-card>
        <x-card class="bg-white/90 dark:bg-gray-900/60">
            <p class="text-sm text-gray-500 dark:text-gray-400">Paid</p>
            <p class="mt-2 text-3xl font-bold text-emerald-500 dark:text-emerald-400">{{ $paidCount }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Refund selesai</p>
        </x-card>
        <x-card class="bg-white/90 dark:bg-gray-900/60">
            <p class="text-sm text-gray-500 dark:text-gray-400">Failed</p>
            <p class="mt-2 text-3xl font-bold text-rose-500 dark:text-rose-400">{{ $failedCount }}</p>
            <p class="text-xs text-gray-400 dark:text-gray-500">Butuh review ulang</p>
        </x-card>
    </div>

    <x-card>
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Antrian Refund</h2>
                <p class="text-sm text-gray-500 dark:text-gray-400">Fokus pada status "Queued" dan "Processing".</p>
            </div>
            <div class="flex flex-wrap gap-2">
                <button class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01" /></svg>
                    Filter Status
                </button>
                <button class="inline-flex items-center gap-2 rounded-full border border-gray-200 px-4 py-2 text-sm font-medium text-gray-700 transition hover:border-gray-300 hover:bg-gray-50 dark:border-gray-700 dark:text-gray-200 dark:hover:bg-gray-800">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
                    Cari Refund
                </button>
            </div>
        </div>
        <div class="mt-6">
            <livewire:admin.refund-queue />
        </div>
    </x-card>
</div>
@endsection

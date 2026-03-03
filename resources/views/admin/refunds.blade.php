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
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Antrian Refund</h2>
        </div>
        <div class="mt-6">
            <livewire:admin.refund-queue />
        </div>
    </x-card>
</div>
@endsection

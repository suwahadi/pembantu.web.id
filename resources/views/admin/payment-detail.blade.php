@extends('layouts.admin', ['title' => 'Detail Payment'])

@section('content')
<div>
    <div class="mb-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">Detail Payment</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Detail pembayaran dan log Midtrans untuk kebutuhan audit</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.payment.index') }}" class="px-4 py-2 rounded-lg flex items-center gap-2 border border-gray-200 bg-white text-gray-700 hover:bg-gray-50 dark:bg-white/10 dark:text-white dark:border-white/10 dark:hover:bg-white/5 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>
    </div>

    <livewire:admin.payment-detail :payment-id="$paymentId" />
</div>
@endsection

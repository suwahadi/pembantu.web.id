@extends('layouts.admin', ['title' => 'Payment'])

@section('content')
<div>
    <div class="mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">History Payment</h1>
                <p class="text-gray-500 dark:text-gray-400 mt-1">Rujukan administrasi keuangan untuk memantau pembayaran order</p>
            </div>
        </div>
    </div>

    <livewire:admin.payment-list />
</div>
@endsection

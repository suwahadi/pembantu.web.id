<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Livewire\Visitor\WorkerSearch;
use App\Livewire\Visitor\CheckoutWizard;
use App\Livewire\Visitor\OrderDetail;
use App\Livewire\Visitor\DisputeForm;
use App\Livewire\Visitor\BankAccounts as VisitorBankAccounts;

use App\Livewire\Admin\Dashboard as AdminDashboard;
use App\Livewire\Admin\DisputeQueue;
use App\Livewire\Admin\RefundQueue;
use App\Livewire\Admin\PayoutQueue;
use App\Livewire\Admin\BankAccounts as AdminBankAccounts;

use App\Livewire\Agency\ContractQueue;
use App\Livewire\Agency\BankAccounts as AgencyBankAccounts;
use App\Livewire\Agency\WorkerList;
use App\Livewire\Agency\WorkerForm;
use App\Livewire\Agency\OrderList;
use App\Livewire\Agency\OrderDetail as AgencyOrderDetail;

use App\Livewire\Pages\Home;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

// Public Routes
Route::get('/', Home::class)->name('home');
Route::get('/cari', WorkerSearch::class)->name('workers.search');

// Checkout Route (Protected)
Route::middleware('auth')->get('/checkout/{worker}', CheckoutWizard::class)->name('checkout');

// Authentication Routes (via Livewire)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Authenticated Visitor Routes
Route::middleware('auth')->group(function () {
    Route::get('/pesanan', fn() => view('orders.list'))->name('orders.list');
    Route::get('/pesanan/{order}', OrderDetail::class)->name('orders.show');
    Route::get('/pesanan/{order}/dispute', DisputeForm::class)->name('orders.dispute');
    Route::get('/rekening-bank', VisitorBankAccounts::class)->name('visitor.bank-accounts');
    
    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();
        return redirect('/');
    })->name('logout');
});

// Admin Routes (Protected by middleware + gate)
Route::middleware(['auth', 'admin-access'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboard::class)->name('dashboard');
    Route::get('/disputes', DisputeQueue::class)->name('disputes');
    Route::get('/refunds', RefundQueue::class)->name('refunds');
    Route::get('/payouts', PayoutQueue::class)->name('payouts');
    Route::get('/bank-accounts', AdminBankAccounts::class)->name('bank-accounts');
});

// Agency Routes (Protected by middleware + gate)
Route::middleware(['auth', 'agency-access'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/', fn() => view('agency.dashboard'))->name('dashboard');
    Route::get('/contracts', ContractQueue::class)->name('contracts');
    Route::get('/workers', WorkerList::class)->name('workers.index');
    Route::get('/workers/create', WorkerForm::class)->name('workers.create');
    Route::get('/workers/{worker}/edit', WorkerForm::class)->name('workers.edit');
    Route::get('/orders', OrderList::class)->name('orders.index');
    Route::get('/orders/{order}', AgencyOrderDetail::class)->name('orders.show');
    Route::get('/bank-accounts', AgencyBankAccounts::class)->name('bank-accounts');
});



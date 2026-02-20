<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

use App\Livewire\Visitor\WorkerSearch;
use App\Livewire\Visitor\CheckoutWizard;
use App\Livewire\Visitor\PaymentMethodSelector;
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

use App\Livewire\Public\WorkerSearchPage;
use App\Livewire\Public\WorkerShowPage;

// Public Routes
Route::get('/', Home::class)->name('home');
Route::get('/cari', WorkerSearch::class)->name('workers.search');
Route::get('/search', WorkerSearchPage::class)->name('search');
Route::get('/worker/{publicId}', WorkerShowPage::class)->name('worker.show');

// Checkout Route (Guest-friendly, auth handled in component)
Route::get('/checkout/{worker}', CheckoutWizard::class)->name('checkout');

// Authentication Routes (via Livewire)
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Authenticated Visitor Routes
Route::middleware('auth')->group(function () {
    Route::get('/profil', \App\Livewire\Profile\UpdateProfile::class)->name('profile');
    Route::get('/pembayaran/{orderId}', PaymentMethodSelector::class)->name('payment.method');
    Route::get('/pesanan', \App\Livewire\Visitor\OrderList::class)->name('orders.list');
    Route::get('/pesanan/{orderId}', OrderDetail::class)->name('orders.show');
    Route::get('/pesanan/{orderId}/dispute', DisputeForm::class)->name('orders.dispute');
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
    Route::get('/', function () {
        return view('admin.dashboard');
    })->name('dashboard');
    Route::get('/disputes', function () {
        return view('admin.disputes');
    })->name('disputes');
    Route::get('/refunds', function () {
        return view('admin.refunds');
    })->name('refunds');
    Route::get('/payouts', function () {
        return view('admin.payouts');
    })->name('payouts');
    Route::get('/bank-accounts', function () {
        return view('admin.bank-accounts');
    })->name('bank-accounts');
});

// Agency Routes (Protected by middleware + gate)
Route::middleware(['auth', 'agency-access'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/', function () {
        return view('agency.dashboard');
    })->name('dashboard');
    Route::get('/contracts', function () {
        return view('agency.contracts');
    })->name('contracts');
    Route::get('/workers', function () {
        return view('agency.workers');
    })->name('workers.index');
    Route::get('/workers/create', function () {
        return view('agency.workers-create');
    })->name('workers.create');
    Route::get('/workers/{worker}/edit', function ($worker) {
        return view('agency.workers-create');
    })->name('workers.edit');
    Route::get('/orders', function () {
        return view('agency.orders');
    })->name('orders.index');
    Route::get('/orders/{orderId}', AgencyOrderDetail::class)->name('orders.show');
    Route::get('/bank-accounts', function () {
        return view('agency.bank-accounts');
    })->name('bank-accounts');
});



<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pages\Home;
use App\Livewire\Pages\Search;
use App\Livewire\Pages\WorkerDetail;
use App\Livewire\Pages\Onboarding;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;

// Public Routes
Route::get('/', Home::class)->name('home');
Route::get('/search', Search::class)->name('search');
Route::get('/worker/{id}', WorkerDetail::class)->name('worker.detail');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/onboarding', Onboarding::class)->name('onboarding');
    Route::get('/dashboard', fn() => view('dashboard'))->name('dashboard');
    Route::get('/orders', fn() => view('orders'))->name('orders');
    Route::post('/order/{worker_id}', fn() => redirect('/dashboard'))->name('order.create');
    Route::post('/logout', function () {
        auth()->logout();
        return redirect('/');
    })->name('logout');
});

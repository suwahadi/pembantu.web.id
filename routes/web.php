<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Marketplace routes
Route::get('/search', function () {
    return view('search.index');
})->name('search');

// Auth routes will be auto-loaded by Laravel

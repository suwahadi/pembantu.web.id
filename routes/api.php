<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json(['message' => 'Pembantu API v1']);
});

// Health check
Route::get('/health', function () {
    return response()->json(['status' => 'ok']);
});

// Midtrans callback
Route::post('/payment/midtrans/notification', 'App\Http\Controllers\Payment\MidtransNotificationController@handleNotification');

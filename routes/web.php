<?php

use Botble\Paymentwall\Http\Controllers\PaymentwallController;
use Illuminate\Support\Facades\Route;

Route::middleware(['core', 'web'])->prefix('payment/paymentwall')->name('payment.paymentwall.')->group(function () {
    Route::get('callback', [PaymentwallController::class, 'callback'])->name('callback');
});

Route::middleware(['core'])->prefix('payment/paymentwall')->name('payment.paymentwall.')->group(function () {
    Route::post('webhook', [PaymentwallController::class, 'webhook'])->name('webhook');
});

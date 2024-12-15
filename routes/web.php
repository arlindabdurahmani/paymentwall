<?php

use Botble\Paymentwall\Http\Controllers\PaymentwallController;

Route::group(['namespace' => 'Botble\Paymentwall\Http\Controllers', 'middleware' => ['web', 'core']], function () {
    // Route to initiate the payment
    Route::post('paymentwall/pay', [PaymentwallController::class, 'pay'])->name('paymentwall.pay');

    // Route for the callback from Paymentwall after payment
    Route::post('paymentwall/callback', [PaymentwallController::class, 'callback'])->name('paymentwall.callback');
});

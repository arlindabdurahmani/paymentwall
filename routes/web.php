<?php

use Botble\Paymentwall\Http\Controllers\PaymentwallController;

Route::group(['namespace' => 'Botble\Paymentwall\Http\Controllers'], function () {
    Route::get('/paymentwall/callback', [PaymentwallController::class, 'handleCallback'])
        ->name('paymentwall.callback');
});

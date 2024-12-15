<?php

namespace Botble\Paymentwall\Providers;

use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    /**
     * Boot hooks
     */
    public function boot()
    {
        add_filter(BASE_FILTER_PAYMENT_METHODS, function ($methods) {
            $methods['paymentwall'] = [
                'title' => 'Paymentwall',
                'description' => 'Pay securely using Paymentwall.',
                'logo' => asset('plugins/paymentwall/images/paymentwall.png'),
            ];

            return $methods;
        });
    }
}

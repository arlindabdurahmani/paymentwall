<?php

namespace Botble\Paymentwall\Services;

use Paymentwall_Base;

class PaymentwallService
{
    /**
     * Initialize Paymentwall API.
     *
     * @return void
     */
    public static function initialize()
    {
        // Set the API type and credentials
        Paymentwall_Base::setApiType(Paymentwall_Base::API_GOODS);
        Paymentwall_Base::setAppKey(env('PAYMENTWALL_PUBLIC_KEY')); // Public Key from .env
        Paymentwall_Base::setSecretKey(env('PAYMENTWALL_SECRET_KEY')); // Secret Key from .env
    }
}

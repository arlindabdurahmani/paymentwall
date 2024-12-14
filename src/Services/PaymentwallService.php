<?php

namespace Botble\Paymentwall\Services;

use Paymentwall_Base;
use Paymentwall_Config;
use Paymentwall_Widget;

class PaymentwallService
{
    public function initialize()
    {
        Paymentwall_Config::getInstance()->set([
            'private_key' => config('plugins.paymentwall.general.private_key'),
            'public_key' => config('plugins.paymentwall.general.public_key'),
        ]);
    }

    public function createWidget($userId, $products, $currency)
    {
        $this->initialize();
        $widget = new Paymentwall_Widget(
            $userId,
            config('plugins.paymentwall.general.widget_code'),
            $products,
            [
                'currency' => $currency,
                'email' => auth()->user()->email ?? 'guest@example.com',
            ]
        );
        return $widget->getHtmlCode();
    }
}

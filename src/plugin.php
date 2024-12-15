<?php

namespace Botble\Paymentwall;

use Botble\PluginManagement\Abstracts\PluginOperationAbstract;
use Botble\Setting\Models\Setting;

class Plugin extends PluginOperationAbstract
{
    /**
     * Remove Paymentwall settings when the plugin is removed.
     */
    public static function remove(): void
    {
        Setting::query()
            ->whereIn('key', [
                'payment_paymentwall_name',
                'payment_paymentwall_description',
                'payment_paymentwall_public_key',
                'payment_paymentwall_secret_key',
                'payment_paymentwall_encryption_key',
                'payment_paymentwall_status',
            ])->delete();
    }
}

<?php

namespace Botble\Paymentwall\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class PaymentwallServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public const MODULE_NAME = 'paymentwall';

    public function boot(): void
    {
        // Ensure the Payment plugin is active
        if (! is_plugin_active('payment')) {
            return;
        }

        // Ensure at least one supported module is active
        if (
            ! is_plugin_active('ecommerce') &&
            ! is_plugin_active('job-board') &&
            ! is_plugin_active('real-estate') &&
            ! is_plugin_active('hotel')
        ) {
            return;
        }

        // Set namespace and load resources
        $this->setNamespace('plugins/paymentwall')
            ->loadAndPublishTranslations()
            ->loadAndPublishViews()
            ->publishAssets()
            ->loadRoutes();

        // Register HookServiceProvider after app boot
        $this->app->booted(function () {
            $this->app->register(HookServiceProvider::class);
        });
    }
}

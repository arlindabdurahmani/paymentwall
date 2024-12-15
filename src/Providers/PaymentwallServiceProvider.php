<?php

namespace Botble\Paymentwall\Providers;

use Illuminate\Support\ServiceProvider;
use Botble\Base\Traits\LoadAndPublishDataTrait;

class PaymentwallServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    /**
     * Register services
     */
    public function register()
    {
        $this->setNamespace('plugins/paymentwall')
            ->loadHelpers();
    }

    /**
     * Boot the plugin
     */
    public function boot()
    {
        $this
            ->loadRoutes(['web']) // Load the routes for the plugin
            ->loadViews() // Load the views for the plugin
            ->publishAssets(); // Publish assets (e.g., images, scripts)
    }
}

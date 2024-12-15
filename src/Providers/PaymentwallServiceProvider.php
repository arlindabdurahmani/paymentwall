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
            ->loadAndPublishViews() // Correct way to load and publish views
            ->publishAssets(); // Publish plugin assets
    }

    /**
     * Load and publish views for the plugin
     *
     * @return $this
     */
    protected function loadAndPublishViews()
    {
        $this->loadViewsFrom(__DIR__ . '/../../resources/views', 'paymentwall');

        return $this;
    }
}

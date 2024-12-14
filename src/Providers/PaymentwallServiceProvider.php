<?php

namespace Botble\Paymentwall\Providers;

use Botble\Base\Traits\LoadAndPublishDataTrait;
use Illuminate\Support\ServiceProvider;

class PaymentwallServiceProvider extends ServiceProvider
{
    use LoadAndPublishDataTrait;

    public function register()
    {
        $this->app->bind('PaymentwallService', function () {
            return new \Botble\Paymentwall\Services\PaymentwallService();
        });
    }

    public function boot()
    {
        $this->setNamespace('plugins/paymentwall')
            ->loadAndPublishConfigurations(['general'])
            ->loadRoutes(['web'])
            ->loadAndPublishViews()
            ->loadAndPublishTranslations()
            ->loadMigrations();
    }
}

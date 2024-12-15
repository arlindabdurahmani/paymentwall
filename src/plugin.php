<?php

namespace Botble\Paymentwall;

use Schema;
use Botble\Base\Interfaces\PluginInterface;

class Plugin implements PluginInterface
{
    /**
     * Activate the plugin
     *
     * @return void
     */
    public static function activate()
    {
        // Add logic for when the plugin is activated
    }

    /**
     * Deactivate the plugin
     *
     * @return void
     */
    public static function deactivate()
    {
        // Add logic for when the plugin is deactivated
    }

    /**
     * Remove the plugin
     *
     * @return void
     */
    public static function remove()
    {
        // Clean up database tables or other resources
        Schema::dropIfExists('paymentwall_transactions');
    }
}

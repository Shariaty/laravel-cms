<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;

class OilMessagesServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('OilMessages', function()
        {
            return new App\Facade\OilMessages;
        });
    }
}

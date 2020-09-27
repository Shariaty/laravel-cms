<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App;

class OilCountryServiceProvider extends ServiceProvider
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
        App::bind('OilCountry', function()
        {
            return new App\Facade\OilCountry;
        });
    }
}

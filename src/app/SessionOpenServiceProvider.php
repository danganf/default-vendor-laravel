<?php

namespace Danganf;

use App\MyClass\SessionOpen;
use Illuminate\Support\ServiceProvider;

class SessionOpenServiceProvider extends ServiceProvider
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
        $this->app->singleton('SessionOpen', function () {
            return $this->app->make('App\MyClass\SessionOpen');
        });
    }
}

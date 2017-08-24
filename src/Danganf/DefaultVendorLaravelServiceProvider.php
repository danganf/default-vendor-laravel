<?php

namespace Danganf;

use Illuminate\Support\ServiceProvider;

class AclServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //$this->publishAll();
    }

    /**
     * Publish the config file to the application config directory
     */
    public function publishAll()
    {
        /*$this->publishes([
            __DIR__ . '/../app/Model/' => base_path('/app/Model'),
        ], 'model');

        $this->publishes([
            __DIR__ . '/../app/Myclass/' => base_path('/app/Myclass'),
        ], 'Myclass');

        $this->publishes([
            __DIR__ . '/../app/Repositories/' => base_path('/app/Repositories'),
        ], 'Repositories');*/

    }
}
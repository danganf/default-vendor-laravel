<?php

namespace IntercaseDefault;

use Illuminate\Support\ServiceProvider;

class LaravelDefaultServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        #CONFIGs
        $this->mergeConfigFrom(__DIR__ . '/config/default.php', 'moddefault');

        #FACADES
        $app = \Illuminate\Foundation\AliasLoader::getInstance();
        $app->alias('IntercaseValidator', 'IntercaseDefault\Facades\IntercaseValidatorFacades');
        $app->alias('ThrowNew'          , 'IntercaseDefault\Facades\ThrowNewExceptionFacades');
        $app->alias('LogDebug'          , 'IntercaseDefault\Facades\LogDebugFacades');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishAll();
    }

    /**
     * Publish the config file to the application config directory
     */
    public function publishAll()
    {
        $this->publishes([
            __DIR__ . '/../publish/app/myclass' => base_path('/app/MyClass'),
        ], 'publishAppMyClass');

        $this->publishes([
            __DIR__ . '/../publish/js/' => base_path('/public/js'),
        ], 'publishAppJs');

        $this->publishes([
            __DIR__ . '/../publish/app/repositories' => base_path('/app/Repositories'),
        ], 'publishAppRepositories');

    }
}
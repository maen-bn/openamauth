<?php namespace Maenbn\OpenAmAuth;

use Illuminate\Contracts\Foundation\Application;
use Illuminate\Auth\Guard;
use Illuminate\Support\ServiceProvider;
use Exception;

class OpenAmAuthServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();

        $this->app['auth']->extend('openam', function ($app) {
            if (!$app['config']['openam']) {
                throw new Exception('OpenAM config not found. Please run ' .
                    'php artisan vendor:publish and check if config/openam.php exists.');
            }

            $config = $app['config']['openam'];

            if ($config['legacy'] == true) {
                return new Guard(new OpenSsoUserProvider($config), $app['session.store']);
            }

            return new Guard(new OpenAmUserProvider($config), $app['session.store']);

        });
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__ . '/../config/openam.php');

        $this->publishes([$source => config_path('openam.php')]);

        $this->mergeConfigFrom($source, 'openam');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }


    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'openam'
        ];
    }
}

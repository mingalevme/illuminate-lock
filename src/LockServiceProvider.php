<?php

namespace Mingalevme\Illuminate\Lock;

use Illuminate\Config\Repository;
use Illuminate\Support\ServiceProvider;

abstract class LockServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('lock.config', function ($app) {
            return $app['config']['lock']
                    ? new Repository($app['config']['lock'])
                    : new Repository(require __DIR__.'/../config/lock.php');
        });
        
        $this->app->singleton('lock', function ($app) {
            return new LockManager($app);
        });
        
        $this->app->singleton('lock.store', function ($app) {
            return $app['lock']->store();
        });
        
        $this->app->singleton('lock.factory', function ($app) {
            return $app['lock']->factory();
        });
    }
    
    /**
     * Boot the service provider.
     */
    abstract public function boot();
    
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            'lock', 'lock.store', 'lock.factory',
        ];
    }
}

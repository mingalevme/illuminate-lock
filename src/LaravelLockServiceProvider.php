<?php

namespace Mingalevme\Illuminate\Lock;

class LaravelLockServiceProvider extends LockServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->publishes([
            __DIR__ . '/../config/lock.php'
                => $this->app->basePath() . '/config/lock.php',
        ], 'config');
    }
}

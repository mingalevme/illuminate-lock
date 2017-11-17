<?php

namespace Mingalevme\Illuminate\Lock;

class LumenLockServiceProvider extends LockServiceProvider
{
    /**
     * Boot the service provider.
     */
    public function boot()
    {
        $this->app->configure('lock');
    }
}

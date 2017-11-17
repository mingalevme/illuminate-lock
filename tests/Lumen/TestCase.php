<?php

namespace Mingalevme\Tests\Illuminate\Lock\Lumen;

use Illuminate\Redis\RedisServiceProvider;
use Laravel\Lumen\Testing\TestCase as LumenTestCase;
use Mingalevme\Illuminate\Lock\LumenLockServiceProvider;

abstract class TestCase extends LumenTestCase
{
    /**
     * Creates the application.
     *
     * @return \Laravel\Lumen\Application
     */
    public function createApplication()
    {
        require_once __DIR__ . '/../../vendor/laravel/lumen/app/Console/Kernel.php';
        
        $app = new \Laravel\Lumen\Application(
            realpath(__DIR__.'/../../vendor/laravel/lumen')
        );
        
        $app->singleton(
            \Illuminate\Contracts\Debug\ExceptionHandler::class,
            \Laravel\Lumen\Exceptions\Handler::class
        );
        $app->singleton(
            \Illuminate\Contracts\Console\Kernel::class,
            \Laravel\Lumen\Console\Kernel::class
        );
        
        $app->withFacades();
        
        $app->register(RedisServiceProvider::class);
        
        $app->register(LumenLockServiceProvider::class);
        
        return $app;
    }
}

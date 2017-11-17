<?php

namespace Mingalevme\Tests\Illuminate\Lock\Laravel;

use Illuminate\Contracts\Console\Kernel;
use Illuminate\Redis\RedisServiceProvider;
use Mingalevme\Illuminate\Lock\LaravelLockServiceProvider;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Creates the application.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function createApplication()
    {
        require_once __DIR__ . '/../../vendor/laravel/laravel/app/Console/Kernel.php';
        $app = require __DIR__ . '/../../vendor/laravel/laravel/bootstrap/app.php';
        $app->make(Kernel::class)->bootstrap();
        $app->register(RedisServiceProvider::class);
        $app->register(LaravelLockServiceProvider::class);
        return $app;
    }
}

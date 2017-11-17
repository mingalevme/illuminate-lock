<?php

namespace Mingalevme\Tests\Illuminate\Lock;

use Mingalevme\Illuminate\Lock\LockManager;
use Mingalevme\Illuminate\Lock\Facades\Lock;
use Mingalevme\Illuminate\Lock\LumenGoogleServiceProvider;
use Mingalevme\Illuminate\Lock\LaravelGoogleServiceProvider;

trait PackageTest
{
    public function testFlockDriver()
    {
        putenv('LOCK_DRIVER=flock');
        putenv('LOCK_FILE_PATH='. sys_get_temp_dir());
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $store = $this->app['lock.store'];
        
        $this->assertInstanceOf(\Symfony\Component\Lock\Store\FlockStore::class, $store);
        
        putenv('LOCK_FILE_PATH=');
    }
    
    public function testMemcachedDriver()
    {
        putenv('LOCK_DRIVER=memcached');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $store = $this->app['lock.store'];
        
        $this->assertInstanceOf(\Symfony\Component\Lock\Store\MemcachedStore::class, $store);
        
        putenv('LOCK_DRIVER=');
    }
    
    public function testRedisDriver()
    {
        putenv('LOCK_DRIVER=redis');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        $this->app['config']->set('database.redis', [
            'default' => [
                'host' => 'localhost',
                'password' => null,
                'port' => 6379,
                'database' => 0,
            ],
        ]);
        
        $store = $this->app['lock.store'];
        
        $this->assertInstanceOf(\Symfony\Component\Lock\Store\RedisStore::class, $store);
        
        putenv('LOCK_DRIVER=');
    }
    
    public function testSemaphoreDriver()
    {
        putenv('LOCK_DRIVER=semaphore');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $store = $this->app['lock.store'];
        
        $this->assertInstanceOf(\Symfony\Component\Lock\Store\SemaphoreStore::class, $store);
        
        putenv('LOCK_DRIVER=');
    }
    
    public function testRetryTillSaveDriver()
    {
        putenv('LOCK_DRIVER=retryTillSave');
        putenv('LOCK_FILE_PATH='. sys_get_temp_dir());
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $store = $this->app['lock.store'];
        
        $this->assertInstanceOf(\Symfony\Component\Lock\Store\RetryTillSaveStore::class, $store);
        
        putenv('LOCK_DRIVER=');
        putenv('LOCK_FILE_PATH=');
    }
    
    public function testCombinedDriver()
    {
        putenv('LOCK_DRIVER=combined');
        putenv('LOCK_FILE_PATH='. sys_get_temp_dir());
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $store = $this->app['lock.store'];
        
        $this->assertInstanceOf(\Symfony\Component\Lock\Store\CombinedStore::class, $store);
        
        putenv('LOCK_DRIVER=');
        putenv('LOCK_FILE_PATH=');
    }
}

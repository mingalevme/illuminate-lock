<?php

namespace Mingalevme\Tests\Illuminate\Lock;

use ReflectionObject;
use Mingalevme\Illuminate\Lock\LockManager;
use Mingalevme\Illuminate\Lock\Facades\Lock;
use Mingalevme\Illuminate\Lock\LumenLockServiceProvider;
use Mingalevme\Illuminate\Lock\LaravelLockServiceProvider;

trait PackageTest
{
    public function setUp()
    {
        parent::setUp();
        putenv('LOCK_FILE_PATH='. sys_get_temp_dir());
        $this->app->bind('path.storage', function () {
            return '/tmp';
        });
    }
    
    public function testDefaultStore()
    {
        Lock::setDefaultStore('semaphore');
        $this->assertInstanceOf(\Symfony\Component\Lock\Store\SemaphoreStore::class, $this->app['lock.store']);
    }
    
    public function testDefaultConfig()
    {
        $this->assertFactorysStoreInstanceOf(\Symfony\Component\Lock\Store\FlockStore::class, $this->app['lock.factory']);
    }
    
    public function testFactoryCaching()
    {
        $this->assertSame(Lock::factory(), Lock::factory());
    }
    
    public function testStoreCaching()
    {
        $this->assertSame(Lock::store(), Lock::store());
    }
    
    public function testFlockDriver()
    {
        $factory = $this->app['lock.factory'];
        $this->assertFactorysStoreInstanceOf(\Symfony\Component\Lock\Store\FlockStore::class, $factory);
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidStore()
    {
        Lock::setDefaultStore('unknown');
        Lock::factory();
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidDriver()
    {
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        $this->app['config']->set('lock.stores.flock.driver', 'unknown');
        Lock::factory();
    }
    
    public function testMemcachedDriver()
    {
        putenv('LOCK_DRIVER=memcached');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $factory = $this->app['lock.factory'];
        
        $this->assertFactorysStoreInstanceOf(\Symfony\Component\Lock\Store\MemcachedStore::class, $factory);
        
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
        
        $factory = $this->app['lock.factory'];
        
        $this->assertFactorysStoreInstanceOf(\Symfony\Component\Lock\Store\RedisStore::class, $factory);
        
        putenv('LOCK_DRIVER=');
    }
    
    public function testSemaphoreDriver()
    {
        putenv('LOCK_DRIVER=semaphore');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $factory = $this->app['lock.factory'];
        
        $this->assertFactorysStoreInstanceOf(\Symfony\Component\Lock\Store\SemaphoreStore::class, $factory);
        
        putenv('LOCK_DRIVER=');
    }
    
    public function testRetryTillSaveDriver()
    {
        putenv('LOCK_DRIVER=retryTillSave');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $factory = $this->app['lock.factory'];
        
        $this->assertFactorysStoreInstanceOf(\Symfony\Component\Lock\Store\RetryTillSaveStore::class, $factory);
        
        putenv('LOCK_DRIVER=');
        putenv('LOCK_FILE_PATH=');
    }
    
    public function testCombinedDriver()
    {
        putenv('LOCK_DRIVER=combined');
        putenv('LOCK_COMBINED_STORES=flock');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        
        $factory = $this->app['lock.factory'];
        
        $this->assertFactorysStoreInstanceOf(\Symfony\Component\Lock\Store\CombinedStore::class, $factory);
        
        putenv('LOCK_DRIVER=');
        putenv('LOCK_FILE_PATH=');
    }
    
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCombinedDriverInvalidStores()
    {
        putenv('LOCK_DRIVER=combined');
        
        $this->app['config']->set('lock', require __DIR__.'/../config/lock.php');
        $this->app['config']->set('lock.stores.combined.stores', null);
        
        Lock::factory();
    }
    
    public function testFacade()
    {
        Lock::setDefaultStore('semaphore');
        $this->assertInstanceOf(\Symfony\Component\Lock\Lock::class, Lock::createLock('test'));
    }
    
    public function testProvides()
    {
        $provides = [
            'lock', 'lock.store', 'lock.factory',
        ];
        $this->assertSame($provides, (new LaravelLockServiceProvider($this->app))->provides());
        $this->assertSame($provides, (new LumenLockServiceProvider($this->app))->provides());
    }
    
    protected function assertFactorysStoreInstanceOf($expectedStoreClass, $factory)
    {
        $prop = (new ReflectionObject($factory))->getProperty('store');
        $prop->setAccessible(true);
        $this->assertInstanceOf($expectedStoreClass, $prop->getValue($factory));
    }
}

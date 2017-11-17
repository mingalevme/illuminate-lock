<?php

namespace Mingalevme\Illuminate\Lock;

use InvalidArgumentException;
use Symfony\Component\Lock\Factory;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\Lock\Store\RedisStore;
use Symfony\Component\Lock\Store\FlockStore;
use Symfony\Component\Lock\Store\CombinedStore;
use Symfony\Component\Lock\Store\SemaphoreStore;
use Symfony\Component\Lock\Store\MemcachedStore;
use Symfony\Component\Lock\Store\RetryTillSaveStore;

class LockManager
{
    /**
     * The application instance.
     *
     * @var \Illuminate\Foundation\Application
     */
    protected $app;
    
    /**
     * The array of resolved stores.
     *
     * @var array
     */
    protected $stores = [];

    /**
     * Create a new Manager instance.
     *
     * @param  \Illuminate\Foundation\Application $app
     * @return void
     */
    public function __construct($app)
    {
        $this->app = $app;
    }

    /**
     * Get a cache pool instance by name.
     *
     * @param  string|null  $name
     * @return mixed
     */
    public function store($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        return isset($this->pools[$name]) ? $this->pools[$name] : ($this->pools[$name] = $this->resolve($name));
    }

    /**
     * Get the lock connection configuration.
     *
     * @param  string  $name
     * @return array
     */
    protected function getConfig($name)
    {
        return $this->app['config']["lock.stores.{$name}"];
    }

    /**
     * Get the default lock driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->app['config']['lock.default'];
    }

    /**
     * Set the default lock driver name.
     *
     * @param  string  $name
     * @return void
     */
    public function setDefaultDriver($name)
    {
        $this->app['config']['lock.default'] = $name;
    }

    /**
     * Resolve the given store.
     *
     * @param  string  $name
     * @return \Symfony\Component\Lock\StoreInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function resolve($name)
    {
        $config = $this->getConfig($name);
        
        if (is_null($config)) {
            throw new InvalidArgumentException("Lock store [{$name}] is not defined.");
        }
        
        $driverMethod = 'create'.ucfirst($config['driver']).'Driver';
        
        if (method_exists($this, $driverMethod)) {
            return new Factory($this->{$driverMethod}($config));
        } else {
            throw new InvalidArgumentException("Driver [{$config['driver']}] is not supported.");
        }
    }

    /**
     * Create an instance of the flock lock driver.
     *
     * @param  array  $config
     * @return FlockStore
     */
    protected function createFlockDriver(array $config)
    {
        return new FlockStore($config['path']);
    }

    /**
     * Create an instance of the memcached lock driver.
     *
     * @param  array  $config
     * @return FlockStore
     */
    protected function createMemcachedDriver(array $config)
    {
        /* @var $memcached \Memcached */
        $memcached = Cache::store($config['connection'])->getStore()->getMemcached();
        return new MemcachedStore($memcached, $config['ttl']);
    }

    /**
     * Create an instance of the redis lock driver.
     *
     * @param  array  $config
     * @return RedisStore
     */
    protected function createRedisDriver(array $config)
    {
        /* @var $connection \Illuminate\Redis\Connections\PredisConnection */
        $connection = \Illuminate\Support\Facades\Redis::connection($config['connection']);
        /* @var $client \Predis\Client */
        $client = $connection->client();
        return new RedisStore($client, (float) $config['ttl']);
    }

    /**
     * Create an instance of the redis lock driver.
     *
     * @param  array  $config
     * @return SemaphoreStore
     */
    protected function createSemaphoreDriver(array $config)
    {
        return new SemaphoreStore();
    }

    /**
     * Create an instance of the RetryTillSaveStore lock driver.
     *
     * @param  array  $config
     * @return RetryTillSaveStore
     */
    protected function createRetryTillSaveDriver(array $config)
    {
        $decorated = $this->store($config['decorated']);
        return new RetryTillSaveStore(
            $decorated, array_get($config, 'retrySleep', 100), array_get($config, 'retryCount', \PHP_INT_MAX));
    }

    /**
     * Create an instance of the CombinedStore lock driver.
     *
     * @param  array  $config
     * @return CombinedStore
     */
    protected function createCombinedDriver(array $config)
    {
        $strategyClass = '\Symfony\Component\Lock\Strategy\\'
                . ucfirst($config['strategy'])
                . 'Strategy';
        $strategy = new $strategyClass();
        
        if (is_string($config['stores'])) {
            $config['stores'] = explode(',', $config['stores']);
        } elseif (!is_array($config['stores'])) {
            throw new \InvalidArgumentException('Invalid config format for "stores" entry');
        }
        
        $stores = [];
        
        foreach ($config['stores'] as $store) {
            $stores[] = $this->store(trim($store));
        }
        
        return new CombinedStore($stores, $strategy);
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param  string  $method
     * @param  array   $parameters
     * @return mixed
     */
    public function __call($method, $parameters)
    {
        return $this->store()->$method(...$parameters);
    }
}

<?php

return [
    
    /*
    |--------------------------------------------------------------------------
    | Default Lock Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default lock connection that gets used while
    | using this locking library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    */
    'default' => env('LOCK_DRIVER', 'flock'),
    
    /*
    |--------------------------------------------------------------------------
    | Lock Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the lock "stores" for your application as
    | well as their drivers. You may even define multiple stores for the
    | same lock driver.
    |
    */
    'stores' => [
        
        'flock' => [
            'driver' => 'flock',
            'path' => env('LOCK_FILE_PATH', storage_path('framework/run')),
        ],
        
        'memcached' => [
            'driver' => 'memcached',
            'connection' => env('LOCK_MEMCACHED_CONNECTION', 'memcached'),
            'ttl' => 300,
        ],
        
        'redis' => [
            'driver' => 'redis',
            'connection' => env('LOCK_REDIS_CONNECTION', 'default'),
            'ttl' => 300,
        ],
        
        'semaphore' => [
            'driver' => 'semaphore',
        ],
        
        'retryTillSave' => [
            'driver' => 'retryTillSave',
            'decorated' => 'flock',
            'retrySleep' => 100,
            'retryCount' => \PHP_INT_MAX,
        ],
        
        'combined' => [
            'driver' => 'combined',
            'stores' => ['flock', 'memcached'],
            'strategy' => 'consensus',
        ],
        
    ],
    
];

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
            'ttl' => env('LOCK_MEMCACHED_TTL', 300),
        ],
        
        'redis' => [
            'driver' => 'redis',
            'connection' => env('LOCK_REDIS_CONNECTION', 'default'),
            'ttl' => env('LOCK_REDIS_TTL', 300),
        ],
        
        'semaphore' => [
            'driver' => 'semaphore',
        ],
        
        'retryTillSave' => [
            'driver' => 'retryTillSave',
            'decorated' => env('LOCK_RETRY_TILL_SAVE_DECORATED', 'flock'),
            'retrySleep' => env('LOCK_RETRY_TILL_SAVE_RETRY_SLEEP', 100),
            'retryCount' => env('LOCK_RETRY_TILL_SAVE_RETRY_COUNT', \PHP_INT_MAX),
        ],
        
        'combined' => [
            'driver' => 'combined',
            'stores' => ['flock', 'memcached'],
            'strategy' => env('LOCK_COMBINED_STRATEGY', 'consensus'),
        ],
        
    ],
    
];

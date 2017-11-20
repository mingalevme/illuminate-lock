# illuminate-lock
Laravel/Lumen decorator for **symfony/lock** component

# Supports drivers:
- Flock
- Memcached
- Redis
- Semaphore
- Combined
- RetryTillSave

# Installation

1. ```composer require mingalevme/illuminate-lock```.

2. Register the appropriate service provider ```\Mingalevme\Illuminate\Lock\LaravelLockServiceProvider::class``` or ```\Mingalevme\Illuminate\Lock\LumenLockServiceProvider::class```.

3. *(Optionally)* Add alias to your bootstrap file:
```php
'Lock' => Mingalevme\Illuminate\Lock\Facades\Lock::class,
```

4. *(Optionally)* For **Larvel** run
```php
php artisan vendor:publish --provider="Mingalevme\Illuminate\Lock\LaravelLockServiceProvider" --tag="config"
``` 
to publish the config file.

5. *(Optionally)* For **Lumen** copy ```/vendor/mingalevme/illuminate-lock/config/lock.php``` to ```/config/lock.php```.

6. Now you are able to use the library:
```php
<?php

use Mingalevme\Illuminate\Lock\Facades\Lock;

$lock = Lock::createLock('resource-id');

if ($lock->acquire()) {
    echo "Resource has been locked";
} else {
    echo "Could not get lock";
}

```

or

```php
<?php

use Mingalevme\Illuminate\Lock\Facades\Lock;

$lock = Lock::store('redis')->createLock('resource-id');

if ($lock->acquire()) {
    echo "Resource has been locked";
} else {
    echo "Could not get lock";
}

```
 
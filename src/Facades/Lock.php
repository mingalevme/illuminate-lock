<?php

namespace Mingalevme\Illuminate\Google\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mingalevme\Illuminate\Lock\LockManager
 */
class Lock extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'lock';
    }
}

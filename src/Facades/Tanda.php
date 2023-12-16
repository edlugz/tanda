<?php

namespace EdLugz\Tanda\Facades;

use Illuminate\Support\Facades\Facade;

class Tanda extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'tanda';
    }
}

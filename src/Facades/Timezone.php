<?php

namespace Laralabs\Timezone\Facades;

use Illuminate\Support\Facades\Facade;

class Timezone extends Facade
{
    /**
     * The name of the binding in the IoC container.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'TimezoneFacade';
    }
}

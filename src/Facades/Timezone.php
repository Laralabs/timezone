<?php

namespace Laralabs\Timezone\Facades;

use Illuminate\Support\Facades\Facade;

class Timezone extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'TimezoneFacade';
    }
}

<?php

namespace Hasab\Facades;

use Illuminate\Support\Facades\Facade;

class Hasab extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'hasab';
    }
}

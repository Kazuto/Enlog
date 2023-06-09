<?php

namespace Kazuto\Enlog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Kazuto\Enlog\Enlog
 */
class Enlog extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Kazuto\Enlog\Enlog::class;
    }
}

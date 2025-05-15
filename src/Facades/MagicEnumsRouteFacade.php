<?php

namespace SynergiTech\MagicEnums\Facades;

use SynergiTech\MagicEnums\Services\MagicEnumsRouteService;
use Illuminate\Support\Facades\Facade;

class MagicEnumsRouteFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return MagicEnumsRouteService::class;
    }
}

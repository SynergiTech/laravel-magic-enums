<?php

namespace SynergiTech\MagicEnums\Facades;

use SynergiTech\MagicEnums\Services\MagicEnumsRouteService;
use Illuminate\Support\Facades\Facade;

/**
 * @method static void enumsController()
 *
 * @see MagicEnumsRouteService
 */
class MagicEnumsRouteFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return MagicEnumsRouteService::class;
    }
}

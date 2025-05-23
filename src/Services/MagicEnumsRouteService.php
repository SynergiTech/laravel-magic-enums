<?php

namespace SynergiTech\MagicEnums\Services;

use Illuminate\Support\Facades\Route;
use SynergiTech\MagicEnums\Http\Controllers\EnumController;

class MagicEnumsRouteService
{
    public function enumsController(): void
    {
        Route::get('/enums', EnumController::class)->name('enums');
    }
}

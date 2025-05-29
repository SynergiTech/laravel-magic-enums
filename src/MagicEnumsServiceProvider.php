<?php

namespace SynergiTech\MagicEnums;

use Illuminate\Support\ServiceProvider;

class MagicEnumsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $basePath = __DIR__ . '/../';
        $configPath = $basePath . 'config/magicenums.php';

        $this->publishes([
            $configPath => config_path('magicenums.php'),
        ], 'config');

         // include the config file from the package if it isn't published
        $this->mergeConfigFrom($configPath, 'magicenums');
    }
}

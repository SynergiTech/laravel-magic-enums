<?php

namespace SynergiTech\MagicEnums;

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Postal\Client;
use SynergiTech\Postal\Controllers\WebhookController;

class MagicEnumsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        $basePath = __DIR__ . '/../';
        $configPath = $basePath . 'config/magicenums.php';

        $this->publishes([
            $configPath => config_path('magicenums.php'),
        ], 'config');
	}
}

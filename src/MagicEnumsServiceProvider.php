<?php

namespace SynergiTech\MagicEnums;

use Illuminate\Support\ServiceProvider;
use SynergiTech\MagicEnums\Commands\GenerateCommand;

class MagicEnumsServiceProvider extends ServiceProvider
{
    public function boot(): void
    {        
        if ($this->app->runningInConsole()) {
            $this->commands(
                [
                GenerateCommand::class,
                ]
            );
        }
    }
}
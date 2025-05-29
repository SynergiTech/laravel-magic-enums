<?php

namespace SynergiTech\MagicEnums\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SynergiTech\MagicEnums\Facades\MagicEnumsRouteFacade;

class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        return [\SynergiTech\MagicEnums\MagicEnumsServiceProvider::class];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        // app_path behaves differently in orchestra testbench
        // so hardcode the path the container expects
        $app['config']->set('magicenums.enum_directory', '/package/app/Enums');
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    public function setUp(): void
    {
        parent::setUp();

        MagicEnumsRouteFacade::enumsController();

        // $this->artisan('migrate')->run();
    }
}

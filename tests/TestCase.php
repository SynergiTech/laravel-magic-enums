<?php

namespace SynergiTech\MagicEnums\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use SynergiTech\MagicEnums\Facades\MagicEnumsRouteFacade;
use Orchestra\Testbench\Concerns\WithWorkbench; 

class TestCase extends OrchestraTestCase
{
    use WithWorkbench; 

    protected function getPackageProviders($app)
    {
        return [\SynergiTech\MagicEnums\MagicEnumsServiceProvider::class];
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);
    }

    public function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testing');
    }

    public function setUp(): void
    {
        parent::setUp();

        // $this->artisan('migrate')->run();
    }
}

<?php

namespace SynergiTech\MagicEnums\Tests\Commands;

use SynergiTech\MagicEnums\Tests\TestCase;

class GenerateCommandTest extends TestCase
{
    public function testCommandExports(): void
    {
        $result = $this->artisan('laravel-magic-enums:generate --input=app/Enums --output=enums');
        $this->assertInstanceOf(\Illuminate\Testing\PendingCommand::class, $result);
        $result->assertSuccessful();
        // $this->assertFileExists('enums/index.js');
    }
}

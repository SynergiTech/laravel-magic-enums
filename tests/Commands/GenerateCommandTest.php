<?php

namespace SynergiTech\MagicEnums\Tests\Commands;

use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\PendingCommand;
use SynergiTech\MagicEnums\Tests\TestCase;

class GenerateCommandTest extends TestCase
{
    public function testCommandExports(): void
    {
        Storage::fake();
        $result = $this->artisan('laravel-magic-enums:generate --input=app/Enums --output=enums');
        $this->assertInstanceOf(PendingCommand::class, $result);
        $result->assertSuccessful();
        $this->assertFileExists('enums/index.js');
    }
}

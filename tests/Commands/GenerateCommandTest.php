<?php

namespace SynergiTech\MagicEnums\Tests\Commands;

use SynergiTech\MagicEnums\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class GenerateCommandTest extends TestCase
{
    public function testCommandExports(): void
    {
        Storage::fake();

        $this
            ->artisan('laravel-magic-enums:generate --input=app/Enums --output=enums --format')
            ->assertExitCode(0);

        $this->assertFileExists('enums/index.js');
    }
}
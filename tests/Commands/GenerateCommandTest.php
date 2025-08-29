<?php

namespace SynergiTech\MagicEnums\Tests\Commands;

use SynergiTech\MagicEnums\Tests\TestCase;
use Illuminate\Support\Facades\Storage;

class GenerateCommandTest extends TestCase
{
    public function testCommandExports(): void
    {
        Storage::fake();

        $result = $this->artisan('laravel-magic-enums:generate --input=app/Enums --output=enums --format');

        $this->assertSame(0, $result);

        $this->assertFileExists('enums/index.js');
    }
}
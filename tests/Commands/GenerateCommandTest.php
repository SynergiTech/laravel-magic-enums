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
        $this->assertInstanceOf(\Illuminate\Testing\PendingCommand::class, $result);
        $result->assertSuccessful();
        $this->assertFileExists('enums/index.js');
    }
}

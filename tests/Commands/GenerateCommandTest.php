<?php

namespace SynergiTech\MagicEnums\Tests\Commands;

use Illuminate\Testing\PendingCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;
use SynergiTech\MagicEnums\Tests\TestCase;

class GenerateCommandTest extends TestCase
{
    public function testCommandExports(): void
    {
        $command = $this->artisan('laravel-magic-enums:generate --input=app/Enums --output=enums');
        $this->assertInstanceOf(PendingCommand::class, $command);
        $result = $command->run();
        $this->assertSame(SymfonyCommand::SUCCESS, $result);
        $this->assertFileExists('./enums/index.js');
    }
}

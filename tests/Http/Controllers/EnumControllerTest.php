<?php

namespace SynergiTech\MagicEnums\Tests\Http\Controllers;

use SynergiTech\MagicEnums\Tests\TestCase;

class EnumControllerTest extends TestCase
{
    public function testInvoke(): void
    {
        $this->getJson(route('enums'))
            ->assertOk();

        $this->assertTrue(true);
    }
}

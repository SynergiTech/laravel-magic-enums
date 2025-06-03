<?php

namespace SynergiTech\MagicEnums\Tests\Http\Controllers;

use SynergiTech\MagicEnums\Tests\TestCase;

class EnumControllerTest extends TestCase
{
    public function testInvoke(): void
    {
        $this->getJson(route('enums'))
            ->assertOk()
            ->assertExactJson([
                "CustomEnum" => [
                    "Alpha" => [
                        "name" => "Alpha",
                        "value" => "alpha",
                        "add_three" => "delta",
                        "something else" => "alpha",
                    ],
                    "Beta" => [
                        "name" => "Beta",
                        "value" => "beta",
                        "add_three" => "echo",
                        "something else" => "beta",
                    ],
                    "Charlie" => [
                        "name" => "Charlie",
                        "value" => "charlie",
                        "add_three" => "foxtrot",
                        "something else" => "charlie",
                    ],
                ],
                'TestingEnum' => [
                    'First' => [
                        'name' => 'First',
                        'value' => 'first',
                        'colours' => 'red',
                    ],
                    'Second' => [
                        'name' => 'Second',
                        'value' => 'second',
                        'colours' => 'yellow',
                    ],
                    'Third' => [
                        'name' => 'Third',
                        'value' => 'third',
                        'colours' => 'green',
                    ],
                    'Fourth' => [
                        'name' => 'Fourth',
                        'value' => 'fourth',
                        'colours' => null,
                    ],
                    'Fifth' => [
                        'name' => 'Fifth',
                        'value' => 'fifth',
                        'colours' => null,
                    ],
                    'Sixth' => [
                        'name' => 'Sixth',
                        'value' => 'sixth',
                        'colours' => null,
                    ],
                    'Seventh' => [
                        'name' => 'Seventh',
                        'value' => 'seventh',
                        'colours' => null,
                    ],
                    'Eighth' => [
                        'name' => 'Eighth',
                        'value' => 'eighth',
                        'colours' => null,
                    ],
                ],
                'TestingEnumThreeQuarters' => [
                    'First' => [
                        'name' => 'First',
                        'value' => 'first',
                        'colours' => 'red',
                    ],
                    'Second' => [
                        'name' => 'Second',
                        'value' => 'second',
                        'colours' => 'yellow',
                    ],
                    'Third' => [
                        'name' => 'Third',
                        'value' => 'third',
                        'colours' => 'green',
                    ],
                    'Fourth' => [
                        'name' => 'Fourth',
                        'value' => 'fourth',
                        'colours' => null,
                    ],
                    'Fifth' => [
                        'name' => 'Fifth',
                        'value' => 'fifth',
                        'colours' => null,
                    ],
                    'Sixth' => [
                        'name' => 'Sixth',
                        'value' => 'sixth',
                        'colours' => null,
                    ],
                ],
            ]);
    }
}

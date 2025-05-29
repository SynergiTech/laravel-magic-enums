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
                'TestingEnum' => [
                    'First' => [
                        'name' => 'First',
                        'value' => 'first',
                        'text' => 'first',
                        'label' => 'first',
                        'colours' => 'red',
                    ],
                    'Second' => [
                        'name' => 'Second',
                        'value' => 'second',
                        'text' => 'second',
                        'label' => 'second',
                        'colours' => 'yellow',
                    ],
                    'Third' => [
                        'name' => 'Third',
                        'value' => 'third',
                        'text' => 'third',
                        'label' => 'third',
                        'colours' => 'green',
                    ],
                    'Fourth' => [
                        'name' => 'Fourth',
                        'value' => 'fourth',
                        'text' => 'fourth',
                        'label' => 'fourth',
                        'colours' => null,
                    ],
                    'Fifth' => [
                        'name' => 'Fifth',
                        'value' => 'fifth',
                        'text' => 'fifth',
                        'label' => 'fifth',
                        'colours' => null,
                    ],
                    'Sixth' => [
                        'name' => 'Sixth',
                        'value' => 'sixth',
                        'text' => 'sixth',
                        'label' => 'sixth',
                        'colours' => null,
                    ],
                    'Seventh' => [
                        'name' => 'Seventh',
                        'value' => 'seventh',
                        'text' => 'seventh',
                        'label' => 'seventh',
                        'colours' => null,
                    ],
                    'Eighth' => [
                        'name' => 'Eighth',
                        'value' => 'eighth',
                        'text' => 'eighth',
                        'label' => 'eighth',
                        'colours' => null,
                    ],
                ],
                'TestingEnumThreeQuarters' => [
                    'First' => [
                        'name' => 'First',
                        'value' => 'first',
                        'text' => 'first',
                        'label' => 'first',
                        'colours' => 'red',
                    ],
                    'Second' => [
                        'name' => 'Second',
                        'value' => 'second',
                        'text' => 'second',
                        'label' => 'second',
                        'colours' => 'yellow',
                    ],
                    'Third' => [
                        'name' => 'Third',
                        'value' => 'third',
                        'text' => 'third',
                        'label' => 'third',
                        'colours' => 'green',
                    ],
                    'Fourth' => [
                        'name' => 'Fourth',
                        'value' => 'fourth',
                        'text' => 'fourth',
                        'label' => 'fourth',
                        'colours' => null,
                    ],
                    'Fifth' => [
                        'name' => 'Fifth',
                        'value' => 'fifth',
                        'text' => 'fifth',
                        'label' => 'fifth',
                        'colours' => null,
                    ],
                    'Sixth' => [
                        'name' => 'Sixth',
                        'value' => 'sixth',
                        'text' => 'sixth',
                        'label' => 'sixth',
                        'colours' => null,
                    ],
                ],
            ]);
    }
}

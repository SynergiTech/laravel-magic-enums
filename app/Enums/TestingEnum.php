<?php

namespace App\Enums;

use SynergiTech\MagicEnums\Attributes\AppendConstToMagic;
use SynergiTech\MagicEnums\Attributes\AppendValueToMagic;
use SynergiTech\MagicEnums\Traits\WithToVueArray;

enum TestingEnum: string
{
    use WithToVueArray;

    case First = 'first';
    case Second = 'second';
    case Third = 'third';
    case Fourth = 'fourth';
    case Fifth = 'fifth';
    case Sixth = 'sixth';
    case Seventh = 'seventh';
    case Eighth = 'eighth';

    /**
     * this makes a sub-enum of this enum in
     * the output with only the listed values
     *
     * typed consts have been available since 8.2
     */
    #[AppendConstToMagic]
    public const THREE_QUARTERS = [
        self::First,
        self::Second,
        self::Third,
        self::Fourth,
        self::Fifth,
        self::Sixth,
    ];

    /**
     * this appends extra properties
     * to each enum value (or null if missing)
     *
     * typed consts have been available since 8.2
     */
    #[AppendValueToMagic]
    public const COLOURS = [
        self::First->value => 'red',
        self::Second->value => 'yellow',
        self::Third->value => 'green',
    ];
}

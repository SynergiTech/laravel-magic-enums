<?php

namespace SynergiTech\MagicEnums\Tests\Enums;

use SynergiTech\MagicEnums\Attributes\AppendConstToMagic;
use SynergiTech\MagicEnums\Attributes\AppendValueToMagic;
use SynergiTech\MagicEnums\Traits\WithToVueArray;

enum JobType: string
{
    use WithToVueArray;

    case Internal = 'internal';
    case Insurance = 'insurance';
    case Logistics = 'logistics';
    case MonthliesCharges = 'monthlies_charges';
    case Sampling = 'sampling';
    case Storage = 'storage';
    case Bespoke = 'bespoke';
    case RateCarded = 'rate_carded';

    // TODO: Remove balls
    #[AppendConstToMagic]
    public const SELECT_BALLS = [
        self::Internal,
        self::Insurance,
        self::Logistics,
        self::MonthliesCharges,
        self::Sampling,
        self::Storage,
        self::Bespoke,
        self::RateCarded,
    ];

    // TODO: Remove colours
    #[AppendValueToMagic]
    public const COLOURS = [
        self::Internal->value => 'bric-red',
        self::Insurance->value => 'bric-yellow',
        self::Sampling->value => 'bric-red',
    ];
}

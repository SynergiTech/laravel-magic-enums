<?php

namespace App\Enums;

use App\Traits\CustomMagic;
use SynergiTech\MagicEnums\Attributes\AppendValueToMagic;
use SynergiTech\MagicEnums\Interfaces\MagicEnum;

enum CustomEnum: string implements MagicEnum
{
    use CustomMagic;

	case Alpha = 'alpha';
    case Beta = 'beta';
    case Charlie = 'charlie';

    #[AppendValueToMagic]
    public const ADD_THREE = [
        self::Alpha->value => 'delta',
        self::Beta->value => 'echo',
        self::Charlie->value => 'foxtrot',
	];
}

<?php

namespace App\Enums;
 
use SynergiTech\MagicEnums\Interfaces\MagicEnum;
use SynergiTech\MagicEnums\Traits\HasMagic;

enum EnumWithSpaces: string implements MagicEnum
{
    use HasMagic;

    case TwentyOne = 'Twenty One';
    case TwentyTwo = 'Twenty Two';

}

<?php

namespace SynergiTech\MagicEnums\PHPStan;

use PHPStan\Reflection\ConstantReflection;
use PHPStan\Rules\Constants\AlwaysUsedClassConstantsExtension;
use SynergiTech\MagicEnums\Interfaces\MagicEnum;

class MagicEnumConstantExtensionVersionOne implements AlwaysUsedClassConstantsExtension
{
    public function isAlwaysUsed(ConstantReflection $constant): bool
    {
        $declaringClass = $constant->getDeclaringClass();

        if (
            $declaringClass->isBackedEnum() &&
            $declaringClass->implementsInterface(MagicEnum::class)
        ) {
            return true;
        }

        return false;
    }
}

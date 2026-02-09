<?php

namespace SynergiTech\MagicEnums\PHPStan;

use PHPStan\Reflection\ConstantReflection;
use PHPStan\Rules\Constants\AlwaysUsedClassConstantsExtension;
use SynergiTech\MagicEnums\Attributes\AppendConstToMagic;
use SynergiTech\MagicEnums\Attributes\AppendValueToMagic;

class MagicEnumConstantExtensionVersionOne implements AlwaysUsedClassConstantsExtension
{
    public function isAlwaysUsed(ConstantReflection $constant): bool
    {
        $attributes = $constant->getAttributes();

        foreach ($attributes as $attribute) {
            $attributeClass = $attribute->getName();
            if (
                $attributeClass === AppendConstToMagic::class ||
                $attributeClass === AppendValueToMagic::class
            ) {
                return true;
            }
        }

        return false;
    }
}

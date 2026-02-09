<?php

if (interface_exists(\PHPStan\Reflection\ClassConstantReflection::class)) {
    $extensionClass = \SynergiTech\MagicEnums\PHPStan\MagicEnumConstantExtension::class;
} else {
    $extensionClass = \SynergiTech\MagicEnums\PHPStan\MagicEnumConstantExtensionVersionOne::class;
}

return [
    'services' => [
        [
            'class' => $extensionClass,
            'tags' => ['phpstan.alwaysUsedClassConstantsExtension'],
        ],
    ],
];
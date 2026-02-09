<?php

if (file_exists('phar://' . __DIR__ . '/vendor/phpstan/phpstan/phpstan.phar/src/Reflection/ClassConstantReflection.php')) {
  $extensionClass = \SynergiTech\MagicEnums\PHPStan\MagicEnumConstantExtension::class;
} else {
  $extensionClass = \SynergiTech\MagicEnums\PHPStan\MagicEnumConstantExtensionVersionOne::class;
}

return [
  'services' => [
    [
      'class' => $extensionClass,
      'tags' => ['phpstan.alwaysUsedClassConstantsExtension']
    ]
  ]
];
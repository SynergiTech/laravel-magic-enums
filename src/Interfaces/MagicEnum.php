<?php

namespace SynergiTech\MagicEnums\Interfaces;

interface MagicEnum
{
    public static function toMagicArray(?array $only = null): array;
}

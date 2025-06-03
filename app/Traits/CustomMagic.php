<?php

namespace App\Traits;

use SynergiTech\MagicEnums\Traits\HasMagic;

trait CustomMagic
{
    use HasMagic {
        HasMagic::toMagicArray as parentToMagicArray;
    }

    /**
     * @param self[]|null $only
     * @return array<string,array<string,mixed>>
     */
    public static function toMagicArray(?array $only = null): array
    {
        $info = self::parentToMagicArray($only);

        foreach (self::cases() as $case) {
            if ($only && ! in_array($case, $only)) {
                continue;
            }

            $info[$case->name] = $info[$case->name] + [
                'something else' => $case->value,
            ];
        }

        return $info;
    }
}

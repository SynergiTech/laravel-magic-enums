<?php

namespace SynergiTech\MagicEnums\Traits;

use ReflectionEnum;
use SynergiTech\MagicEnums\Attributes\AppendConstToMagic;
use SynergiTech\MagicEnums\Attributes\AppendValueToMagic;

trait WithToVueArray
{
    /**
     * @param self[]|null $only
     * @return array<string,array<string,mixed>>
     */
    public static function toVueArray(?array $only = null): array
    {
        $info = [];

        foreach (self::cases() as $case) {
            if ($only && ! in_array($case, $only)) {
                continue;
            }

            $data = [
                'name' => $case->name,
                'value' => $case->value,
                'text' => $case->value,
                'label' => $case->value,
            ];

            foreach (self::getValues() as $value) {
                $array = constant('self::' . $value);
                throw_if(!is_array($array), new \Exception('Reflection constant not found'));
                $data[strtolower($value)] = $array[$case->value] ?? null;
            }

            $info[$case->name] = $data;
        }

        return $info;
    }

    /**
     * @return array<string>
     */
    private static function getConstants(): array
    {
        static $constants = null;

        if (is_null($constants)) {
            $reflection = new ReflectionEnum(self::class);
            $constants = $reflection->getConstants();
            $constants = array_filter($constants, fn ($c) => !($c instanceof self));
            $constants = array_keys($constants);
        }

        return $constants;
    }

    /**
     * @return array<string>
     */
    private static function getValues(): array
    {
        static $values = null;

        if (is_null($values)) {
            $reflection = new ReflectionEnum(self::class);
            $constants = self::getConstants();
            $values = array_filter(
                $constants,
                function ($c) use ($reflection) {
                    $reflected = $reflection->getReflectionConstant($c);
                    throw_if(! $reflected, new \Exception('Reflection constant not found'));
                    return ! empty($reflected->getAttributes(AppendValueToMagic::class));
                }
            );
        }

        return $values;
    }

    /**
     * @return array<string>
     */
    public static function getConsts(): array
    {
        static $values = null;

        if (is_null($values)) {
            $reflection = new ReflectionEnum(self::class);
            $constants = self::getConstants();
            $values = array_filter(
                $constants,
                function ($c) use ($reflection) {
                    $reflected = $reflection->getReflectionConstant($c);
                    throw_if(! $reflected, new \Exception('Reflection constant not found'));
                    return ! empty($reflected->getAttributes(AppendConstToMagic::class));
                }
            );
        }

        return $values;
    }
}

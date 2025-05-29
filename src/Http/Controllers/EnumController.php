<?php

namespace SynergiTech\MagicEnums\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class EnumController
{
    public function __invoke(): Response|JsonResponse
    {
        $cache = Cache::driver('file');
        $versioned = file_exists('/var/www/VERSION');
        $time = $versioned ? filemtime('/var/www/VERSION') : -1;

        $cache->put('enums_last_modified', $time);

        if (app()->runningUnitTests() || app()->isLocal() || $time > $cache->get('enums_last_modified', 0)) {
            $cache->forget('enums');
        }

        $enums = $cache->rememberForever('enums', function () {
            $values = [];

            /** @var iterable<string,\SplFileInfo> */
            $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(config('magicenums.enum_directory')));

            /** @var array<string,string> $enums */
            $enums = collect($paths)
                ->reject(fn ($i) => $i->isDir() || str_ends_with($i->getRealPath(), '/..'))
                ->map(function ($item) {
                    // Build the class name from the file path.
                    return rtrim(str_replace(DIRECTORY_SEPARATOR, '\\', ucfirst(str_replace(getcwd() . '/', '', $item->getRealPath()))), '.php');
                })
                ->filter(fn ($i) => method_exists($i, 'toVueArray'))
                ->values()
                ->toArray();

            foreach ($enums as $class) {
                $classKey = str_replace([config('magicenums.enum_namespace'), '\\'], '', $class);

                $values[$classKey] = $class::toVueArray();
                foreach ($class::getConsts() as $exposed) {
                    $constKey = Str::of($exposed)->lower()->studly();
                    $values[$classKey . $constKey] = $class::toVueArray(only: constant("{$class}::{$exposed}"));
                }
            }

            /** @var array<string,array<string,string>> $values */
            return $values;
        });

        return response()->json($enums);
    }
}

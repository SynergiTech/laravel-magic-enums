<?php

namespace SynergiTech\MagicEnums;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;  
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SynergiTech\MagicEnums\Interfaces\MagicEnum;

class GenerateCommand extends Command
{
    protected $signature = 'laravel-magic-enums:generate {--input} {--output=}';
    protected $description = 'Generate a JSON file containing all MagicEnum values.';

    public function __construct(
        private Filesystem $files
    ) {
        parent::__construct();
    }


    protected function readEnumsAsJson(string $path)
    {
        $values = [];

        /** @var iterable<string,\SplFileInfo> */
        $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        /** @var array<string,string> $enums */
        $enums = collect($paths)
            ->reject(fn ($i) => $i->isDir() || str_ends_with($i->getRealPath(), '/..'))
            ->map(function ($item) {
                // Build the class name from the file path.
                $cwd = (app()->runningUnitTests()) ? getcwd() : base_path();

                $path = ucfirst(str_replace($cwd . '/', '', $item->getRealPath()));
                return rtrim(str_replace(DIRECTORY_SEPARATOR, '\\', $path), '.php');
            })
            ->filter(function ($i) {
                $cases = $i::cases();
                return reset($cases) instanceof MagicEnum;
            })
            ->values()
            ->toArray();

        foreach ($enums as $class) {
            $classKey = str_replace([config('magicenums.enum_namespace'), '\\'], '', $class);

            $values[$classKey] = $class::toMagicArray();
            foreach ($class::getConsts() as $exposed) {
                $constKey = Str::of($exposed)->lower()->studly();
                $values[$classKey . $constKey] = $class::toMagicArray(only: constant("{$class}::{$exposed}"));
            }
        }

        /** @var array<string,array<string,string>> $values */
        return json_encode($values);
    }

    protected function writeFiles($path, $content)
    {
        $this->files->ensureDirectoryExists(dirname($path));
        $this->files->put($path, $content);
    }

    public function handle()
    {
        $output = $this->readEnumsAsJson($this->option('input'));
        $this->writeFiles($this->option('output'), $output);
    }
}

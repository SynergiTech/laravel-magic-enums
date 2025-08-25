<?php

namespace SynergiTech\MagicEnums\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem; 
use Illuminate\Support\Str;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SynergiTech\MagicEnums\Interfaces\MagicEnum;

use function Illuminate\Filesystem\join_paths;

class GenerateCommand extends Command
{
    protected $signature = 'laravel-magic-enums:generate {--input=app/Enums} {--output=resources/js/magic-enums} {--format} {--prettier=}';
    protected $description = 'Export enums to your frontend.';

    public function __construct(
        private Filesystem $files
    ) {
        parent::__construct();
    }

    public function handle()
    {  
        $output = $this->readEnumsAsJson($this->base());
        $this->writeFiles($this->option('output'), $output);

        if ($this->option('format')) {
            $this->runPrettier($this->option('output'));
        }
    }
    
    private function readEnumsAsJson(string $path)
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

    private function jsFilePath(string $path): string
    {
        return join_paths($path, 'index.js');
    }

    private function dtsFilePath(string $path): string
    {
        return join_paths($path, 'useEnums.d.ts');
    }

    private function writeFiles($path, $content)
    { 
        $jsContent = <<<JAVASCRIPT
export const enums = {$content};
JAVASCRIPT;

        $this->files->ensureDirectoryExists(dirname($this->option('input')));

        if ($this->files->exists($path)) {
            $this->files->deleteDirectory($path);
        }
 
        $this->files->makeDirectory($path); 

        $this->files->put($this->jsFilePath($path), $jsContent);

        $dtsContent = <<<TYPESCRIPT
import { enums } from '.';

declare module 'useEnums.ts' {
  export function useEnums(): typeof enums;
}
TYPESCRIPT;

        $this->files->put($this->dtsFilePath($path), $dtsContent);

        $this->info("Wrote enums to {$this->jsFilePath($path)}!");
    }

    private function runPrettier(string $path, string $prettierCommand = 'npx prettier'): void
    {
        $prettier = $this->option('prettier') ?: $prettierCommand;

        exec("{$prettier} --write {$path}");
    }

    private function base(): string
    {
        return join_paths(base_path(), $this->option('input'));
    }
}

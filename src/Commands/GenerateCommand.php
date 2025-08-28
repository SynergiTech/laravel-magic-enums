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
 
        $rootNamespace = $this->determineRootNamespace($enums);

        foreach ($enums as $class) {
            $classKey = Str::of($class)
                ->chopStart($rootNamespace)
                ->replace('\\', '')
                ->toString();

            $values[$classKey] = $class::toMagicArray();

            foreach ($class::getConsts() as $exposed) {
                $constKey = Str::of($exposed)
                    ->lower()
                    ->studly();

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

    private function writeFiles($path, $content)
    { 
        $jsContent = <<<JAVASCRIPT
export const enums = {$content}; 
for (const key in enums) {
    enums[key] = new Proxy(enums[key], {
        get(target, prop) {
            if (typeof prop !== 'string') {
                return false;
            }

            const normalisedKey = prop.replaceAll(' ', '');

            if (Reflect.has(target, normalisedKey)) {
                return Reflect.get(target, normalisedKey);
            }

            return false;
        },
    });
}

// Prevent mutations.
Object.freeze(enums); 
JAVASCRIPT;

        $this->files->ensureDirectoryExists(dirname($this->option('input')));

        if ($this->files->exists($path)) {
            $this->files->deleteDirectory($path);
        }
 
        $this->files->makeDirectory($path); 

        $this->files->put($this->jsFilePath($path), $jsContent);

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

    private function determineRootNamespace(array $classes): string
    {
        // Loop through the array of enum class names. Find the root namespace that all enums share.
        // This is done by finding the longest common prefix of all class names.
        // Then, remove that prefix from each class name to get the relative class name.
        // Finally, use that relative class name as the key in the output array.
        if (count($classes) === 0) {
            return '';
        }

        $commonPrefix = $classes[0];
        foreach ($classes as $class) {
            $i = 0;
            while (isset($commonPrefix[$i], $class[$i]) && $commonPrefix[$i] === $class[$i]) {
                $i++;
            }
            $commonPrefix = substr($commonPrefix, 0, $i);
        }
        // Ensure prefix ends at a namespace separator
        $lastSep = strrpos($commonPrefix, '\\');
        if ($lastSep !== false) {
            return substr($commonPrefix, 0, $lastSep + 1);
        }
        return '';
    }
}

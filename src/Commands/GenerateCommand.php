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
    protected $signature = 'laravel-magic-enums:generate 
        {--input=app/Enums}
        {--output=resources/js/magic-enums}
        {--format=false}
        {--prettier=}';

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

        /**
 * @var iterable<string,\SplFileInfo>
*/
        $paths = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path));

        /**
 * @var array<string,string> $enums
*/
        $enums = collect($paths)
            ->reject(fn ($i) => $i->isDir() || str_ends_with($i->getRealPath(), '/..'))
            ->map(
                function ($item) {
                    return $this->fqcnFromPath($item->getRealPath());
                }
            )
            ->filter(
                function ($i) {
                    $cases = $i::cases();
                    return reset($cases) instanceof MagicEnum;
                }
            )
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

        /**
 * @var array<string,array<string,string>> $values
*/
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
Object.freeze(enums); 
JAVASCRIPT;

        $this->files->ensureDirectoryExists(dirname($this->option('input')));

        if ($this->files->exists($path)) {
            $this->files->deleteDirectory($path);
        }

        $this->files->makeDirectory(
            path: $path,
            recursive: true,
        );

        $this->files->put($this->jsFilePath($path), $jsContent);

        $this->info("Wrote enums to {$this->jsFilePath($path)}!");
    }

    private function runPrettier(string $path, string $prettierCommand = 'npx prettier'): void
    {
        $prettier = $this->option('prettier') ?: $prettierCommand;
        exec("{$prettier} {$path} --write");
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

    protected function fqcnFromPath(string $path): string
    {
        $namespace = $class = $buffer = '';

        $handle = fopen($path, 'r');

        while (!feof($handle)) {
            $buffer .= fread($handle, 512);

            // Suppress warnings for cases where `$buffer` ends in the middle of a PHP comment.
            $tokens = @token_get_all($buffer);

            // Filter out whitespace and comments from the tokens, as they are irrelevant.
            $tokens = array_filter($tokens, fn($token) => $token[0] !== T_WHITESPACE && $token[0] !== T_COMMENT);

            // Reset array indexes after filtering.
            $tokens = array_values($tokens);

            foreach ($tokens as $index => $token) {
                // The namespace is a `T_NAME_QUALIFIED` that is immediately preceded by a `T_NAMESPACE`.
                if (
                    $token[0] === T_NAMESPACE && isset($tokens[$index + 1])
                    && $tokens[$index + 1][0] === T_NAME_QUALIFIED
                ) {
                    $namespace = $tokens[$index + 1][1];
                    continue;
                }

                // The class name is a `T_STRING` which makes it unreliable to match against, so check if we have a
                // `T_ENUM` token with a `T_STRING` token ahead of it.
                if ($token[0] === T_ENUM && isset($tokens[$index + 1]) && $tokens[$index + 1][0] === T_STRING) {
                    $class = $tokens[$index + 1][1];
                }
            }

            if ($namespace && $class) {
                // We've found both the namespace and the class, we can now stop reading and parsing the file.
                break;
            }
        }

        fclose($handle);
        return $namespace . '\\' . $class;
    }
}

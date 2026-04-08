<?php

namespace SynergiTech\MagicEnums\Tests\Commands;

use Illuminate\Filesystem\Filesystem;
use ReflectionMethod;
use SynergiTech\MagicEnums\Commands\GenerateCommand;
use SynergiTech\MagicEnums\Tests\TestCase;

class FqcnFromPathTest extends TestCase
{
    private GenerateCommand $command;

    private ReflectionMethod $method;

    /** @var string[] */
    private array $tempFiles = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->command = new GenerateCommand(new Filesystem());
        $this->method = new ReflectionMethod(GenerateCommand::class, 'fqcnFromPath');
        $this->method->setAccessible(true);
    }

    public function tearDown(): void
    {
        foreach ($this->tempFiles as $file) {
            if (file_exists($file)) {
                unlink($file);
            }
        }

        parent::tearDown();
    }

    public function testBasicEnumParsing(): void
    {
        $path = $this->createTempEnum(<<<'PHP'
<?php

namespace App\Enums;

enum TestEnum: string
{
    case First = 'first';
    case Second = 'second';
}
PHP);

        $result = $this->method->invoke($this->command, $path);

        $this->assertSame('App\Enums\TestEnum', $result);
    }

    public function testEnumNameAtTokenBoundaryIsParsedCorrectly(): void
    {
        // Build a file where "enum TestEnum" straddles the old 512-byte buffer boundary.
        // The old implementation read 512 bytes at a time and tokenized each chunk,
        // which would split "TestEnum" into "TestE" + "num" (or similar) causing a parse failure.
        $header = "<?php\n\nnamespace App\\Enums;\n\n";
        $enumDeclaration = "enum TestEnum: string\n{\n    case First = 'first';\n}\n";

        // Pad with a PHP comment so that "enum TestEnum" starts just before byte 512,
        // placing the enum name itself across the boundary.
        $targetOffset = 503; // "enum " is 5 bytes, so "TestEnum" starts at 512
        $paddingNeeded = $targetOffset - strlen($header);
        $comment = '// ' . str_repeat('x', $paddingNeeded - 4) . "\n"; // -4 for "// " and "\n"

        $content = $header . $comment . $enumDeclaration;

        // Verify our padding puts "TestEnum" right at byte 512.
        $enumNamePos = strpos($content, 'TestEnum');
        $this->assertSame(508, $enumNamePos, sprintf(
            'Expected (Test)Enum at byte 512, but found it at byte %d. Adjust padding.',
            $enumNamePos
        ));

        $path = $this->createTempEnum($content);
        $result = $this->method->invoke($this->command, $path);

        $this->assertSame('App\Enums\TestEnum', $result);
    }

    private function createTempEnum(string $content): string
    {
        $path = tempnam(sys_get_temp_dir(), 'enum_test_') . '.php';
        file_put_contents($path, $content);
        $this->tempFiles[] = $path;

        return $path;
    }
}

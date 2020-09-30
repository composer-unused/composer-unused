<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Parser\PHP;

use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use function iterator_to_array;

final class SymbolNameParserTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldParseOnlyClasses(): void
    {
        $code = <<<CODE
        <?php

        namespace Test;

        use Nette\Database;

        final class MyClass {}
        CODE;

        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            new ForeignSymbolCollector()
        );

        $symbols = iterator_to_array($symbolNameParser->parseSymbolNames($code));

        self::assertCount(1, $symbols);
        self::assertSame('Test\MyClass', $symbols[0]);
    }
}

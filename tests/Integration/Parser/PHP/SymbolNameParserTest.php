<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Parser\PHP;

use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;

use function iterator_to_array;

class SymbolNameParserTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldParseClasses(): void
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            new ForeignSymbolCollector()
        );

        $code = <<<CODE
        <?php
        declare(strict_types=1);

        namespace Test\Sub;

        class TestClass {
            public function test() {}
        }
        CODE;


        $symbolNames = iterator_to_array($symbolNameParser->parseSymbolNames($code));
        self::assertCount(1, $symbolNames);
        self::assertContains('Test\\Sub\\TestClass', $symbolNames);
    }

    /**
     * @test
     */
    public function itShouldParseFunctions(): void
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            new ForeignSymbolCollector()
        );

        $code = <<<CODE
        <?php
        declare(strict_types=1);

        function testfunction1() {}
        function testfunction2() {}
        CODE;


        $symbolNames = iterator_to_array($symbolNameParser->parseSymbolNames($code));

        self::assertCount(2, $symbolNames);
        self::assertContains('testfunction2', $symbolNames);
    }

    /**
     * @test
     */
    public function itShouldParseConstants(): void
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            new ForeignSymbolCollector()
        );

        $code = <<<CODE
        <?php
        declare(strict_types=1);

        const TESTCONST1 = 'string';
        const TESTCONST2 = 1;
        const TESTCONST3 = 1.2;
        CODE;


        $symbolNames = iterator_to_array($symbolNameParser->parseSymbolNames($code));

        self::assertCount(3, $symbolNames);
        self::assertContains('TESTCONST3', $symbolNames);
    }
}

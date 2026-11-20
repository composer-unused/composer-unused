<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Integration\Parser\PHP;

use ComposerUnused\SymbolParser\Parser\PHP\ConsumedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\DefinedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use PhpParser\NodeVisitor\NameResolver;
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
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            new DefinedSymbolCollector()
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
    public function itShouldParseInterfaces(): void
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            new DefinedSymbolCollector()
        );

        $code = <<<CODE
        <?php
        declare(strict_types=1);

        namespace Test\Sub;

        interface TestInterface {
            public function test();
        }
        CODE;


        $symbolNames = iterator_to_array($symbolNameParser->parseSymbolNames($code));
        self::assertCount(1, $symbolNames);
        self::assertContains('Test\\Sub\\TestInterface', $symbolNames);
    }

    /**
     * @test
     */
    public function itShouldParseFunctions(): void
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            new DefinedSymbolCollector()
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
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            new DefinedSymbolCollector()
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

    /**
     * @test
     */
    public function itShouldParseDefinedConstants(): void
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            new DefinedSymbolCollector()
        );

        $code = <<<CODE
        <?php
        declare(strict_types=1);

        define('TESTDEFINED', 1);
        CODE;

        $symbolNames = iterator_to_array($symbolNameParser->parseSymbolNames($code));

        self::assertCount(1, $symbolNames);
        self::assertContains('TESTDEFINED', $symbolNames);
    }

    /**
     * @test
     */
    public function itShouldFindConsumedConstants(): void
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            new ConsumedSymbolCollector([new ConstStrategy()])
        );

        $code = <<<CODE
        <?php
        declare(strict_types=1);

        function test() {
            return TESTCONSTANT;
        }
        CODE;

        $symbolNames = iterator_to_array($symbolNameParser->parseSymbolNames($code));

        self::assertCount(1, $symbolNames);
        self::assertContains('TESTCONSTANT', $symbolNames);
    }
}

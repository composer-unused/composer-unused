<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP;

use ComposerUnused\SymbolParser\Parser\PHP\DefinedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\NameResolver;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ExtendsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ImplementsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Test\ParserTestCase;
use PhpParser\ParserFactory;

final class SymbolNameParserTest extends ParserTestCase
{
    /**
     * @test
     */
    public function itShouldParseDefinedSymbols(): void
    {
        $code = <<<CODE
        <?php

        namespace Test;

        use Nette\Database;

        final class MyClass {}
        CODE;

        $symbols = $this->parseDefinedSymbols($code);

        self::assertCount(1, $symbols);
        self::assertSame('Test\MyClass', $symbols[0]);
    }

    /**
     * @test
     */
    public function itShouldParseDefinedFunctions(): void
    {
        $code = <<<CODE
        <?php

        function testfunction() {}
        CODE;

        $symbols = $this->parseDefinedSymbols($code);

        self::assertCount(1, $symbols);
        self::assertSame('testfunction', $symbols[0]);
    }

    /**
     * @test
     */
    public function itShouldConsolidateSymbols(): void
    {
        $code = <<<CODE
        <?php

        namespace Testing;

        use My\NameSpace1; // partial foreign namespace use
        use B\NS\MyClass; // foreign FQN

        class Foo extends MyClass implements NameSpace1\Bar, \Other\Namespace2\Baz {
        }
        CODE;

        $symbols = $this->parseConsumedSymbols(
            [
                new UseStrategy(),
                new ExtendsParseStrategy(),
                new ImplementsParseStrategy(),
            ],
            $code
        );

        self::assertCount(4, $symbols);
        self::assertSame('My\NameSpace1', $symbols[0]);
        self::assertSame('B\NS\MyClass', $symbols[1]);
        self::assertSame('My\NameSpace1\Bar', $symbols[2]);
        self::assertSame('Other\Namespace2\Baz', $symbols[3]);
    }

    /**
     * @test
     * @link https://github.com/composer-unused/symbol-parser/issues/122
     */
    public function itShouldParseDefinedClosureDirectCalls(): void
    {
        $code = <<<'CODE'
        <?php
        $handlers = [
            function () { echo "Hello Handler\n"; },
        ];

        foreach ($handlers as $handler) {
            if (is_callable($handler)) {
                $handler();
            }
        }
        CODE;

        $symbols = $this->parseDefinedSymbols($code);

        self::assertCount(0, $symbols);
    }

    /**
     * @test
     * @link https://github.com/composer-unused/symbol-parser/issues/136
     */
    public function itShouldParseDefinedClassLikeSymbols(): void
    {
        $code = <<<CODE
        <?php
        // @link https://www.php.net/manual/en/language.oop5.traits.php

        trait ezcReflectionReturnInfo {
            function getReturnType() { /*1*/ }
            function getReturnDescription() { /*2*/ }
        }

        class ezcReflectionMethod extends ReflectionMethod {
            use ezcReflectionReturnInfo;
            /* ... */
        }

        class ezcReflectionFunction extends ReflectionFunction {
            use ezcReflectionReturnInfo;
            /* ... */
        }

        // @link https://www.php.net/manual/en/language.enumerations.basics.php
        enum Suit
        {
            case Hearts;
            case Diamonds;
            case Clubs;
            case Spades;
        }
        CODE;

        $symbols = $this->parseDefinedSymbols($code);

        self::assertCount(4, $symbols);
        self::assertSame('ezcReflectionReturnInfo', $symbols[0]);
        self::assertSame('ezcReflectionMethod', $symbols[1]);
        self::assertSame('ezcReflectionFunction', $symbols[2]);
        self::assertSame('Suit', $symbols[3]);
    }


    /**
     * @test
     * @link https://github.com/composer-unused/symbol-parser/issues/136
     */
    public function itSkipsAlreadyVisitedFiles(): void
    {
        /* @Given a symbol collector which follows includes. */
        $includes = [];
        $collector = new DefinedSymbolCollector();
        $collector->setFileIncludeCallback(function ($file) use (&$includes) {
            array_push($includes, $file->getPath());
        });
        $parser = new SymbolNameParser(
            (new ParserFactory())->createForNewestSupportedVersion(),
            new NameResolver(),
            $collector
        );

        /* @When the source code includes the same file multiple times. */
        $code = <<<CODE
        <?php

        include 'test1.php';
        include 'test2.php';
        include 'test3.php';
        include 'test1.php'; // should get skipped
        include 'test2.php'; // should get skipped
        include 'test3.php'; // should get skipped
        CODE;
        iterator_to_array($parser->parseSymbolNames($code));

        /* @And the symbol parser (+ collector) does multiple passes on the source code. */
        iterator_to_array($parser->parseSymbolNames($code));

        /* @Then the collector only followed each unique file once. */
        self::assertCount(3, $includes);
        self::assertEquals(['test1.php', 'test2.php', 'test3.php'], $includes);
    }
}

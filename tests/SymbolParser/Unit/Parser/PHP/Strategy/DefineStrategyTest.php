<?php

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP\Strategy;

use ComposerUnused\SymbolParser\Parser\PHP\Strategy\DefineStrategy;
use ComposerUnused\SymbolParser\Test\ParserTestCase;

/**
 * @author Laurent Laville
 */
final class DefineStrategyTest extends ParserTestCase
{
    private DefineStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new DefineStrategy();
    }

    /**
     * @test
     */
    public function itShouldParseConsumedNamespaces(): void
    {
        $code = <<<CODE
        <?php
        // @link https://www.php.net/manual/en/language.namespaces.importing.php

        namespace foo;

        use My\Full\Classname as Another;

        // this is the same as use My\Full\NSname as NSname
        use My\Full\NSname;

        // importing a global class
        use ArrayObject;

        // importing a function
        use function My\Full\functionName;

        // aliasing a function
        use function My\Full\functionName as func;

        // importing a constant
        use const My\Full\CONSTANT;
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertCount(1, $symbols);
        self::assertSame('foo', $symbols[0]);
    }

    /**
     * @test
     */
    public function itShouldParseConsumedInterfaces(): void
    {
        $code = <<<CODE
        <?php
        // @link https://www.php.net/manual/en/language.oop5.interfaces.php

        interface A
        {
            public function foo();
        }

        interface B extends A
        {
            public function baz(Baz \$baz);
        }

        // This will work
        class C implements B
        {
            public function foo()
            {
            }

            public function baz(Baz \$baz)
            {
            }
        }

        // This will not work and result in a fatal error
        class D implements B
        {
            public function foo()
            {
            }

            public function baz(Foo \$foo)
            {
            }
        }
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertCount(4, $symbols);
        self::assertSame('A', $symbols[0]);
        self::assertSame('B', $symbols[1]);
        self::assertSame('C', $symbols[2]);
        self::assertSame('D', $symbols[3]);
    }

    /**
     * All kind of class : that's included class, trait and enum
     * @test
     */
    public function itShouldParseConsumedClasses(): void
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

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertCount(4, $symbols);
        self::assertSame('ezcReflectionReturnInfo', $symbols[0]);
        self::assertSame('ezcReflectionMethod', $symbols[1]);
        self::assertSame('ezcReflectionFunction', $symbols[2]);
        self::assertSame('Suit', $symbols[3]);
    }

    /**
     * @test
     */
    public function itShouldParseConsumedConstants(): void
    {
        $code = <<<CODE
        <?php
        // @link https://www.php.net/manual/en/language.constants.php

        // Valid constant names
        define("FOO",     "something");
        define("FOO2",    "something else");
        define("FOO_BAR", "something more");

        // Invalid constant names
        define("2FOO",    "something");

        // This is valid, but should be avoided:
        // PHP may one day provide a magical constant
        // that will break your script
        define("__FOO__", "something");
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertCount(5, $symbols);
        self::assertSame('FOO', $symbols[0]);
        self::assertSame('FOO2', $symbols[1]);
        self::assertSame('FOO_BAR', $symbols[2]);
        self::assertSame('2FOO', $symbols[3]);
        self::assertSame('__FOO__', $symbols[4]);
    }
}

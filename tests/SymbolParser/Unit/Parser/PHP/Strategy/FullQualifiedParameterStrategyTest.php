<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP\Strategy;

use ComposerUnused\SymbolParser\Parser\PHP\Strategy\FullQualifiedParameterStrategy;
use ComposerUnused\SymbolParser\Test\ParserTestCase;

final class FullQualifiedParameterStrategyTest extends ParserTestCase
{
    private FullQualifiedParameterStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new FullQualifiedParameterStrategy();
    }

    /**
     * @test
     */
    public function itShouldParseFQNFunctionParameter(): void
    {
        $code = <<<CODE
        <?php

        namespace Testing;

        class Foo {
            public function __construct(private readonly \My\Namespace\Bar1 \$parameter) {}
            public function test(\My\Namespace\Bar2 \$parameter) {}
        }
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertCount(2, $symbols);
        self::assertSame('My\Namespace\Bar1', $symbols[0]);
        self::assertSame('My\Namespace\Bar2', $symbols[1]);
    }
}

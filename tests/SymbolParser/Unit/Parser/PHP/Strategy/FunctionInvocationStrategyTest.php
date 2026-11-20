<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP\Strategy;

use ComposerUnused\SymbolParser\Parser\PHP\Strategy\FunctionInvocationStrategy;
use ComposerUnused\SymbolParser\Test\ParserTestCase;

final class FunctionInvocationStrategyTest extends ParserTestCase
{
    private FunctionInvocationStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new FunctionInvocationStrategy();
    }

    /**
     * @test
     */
    public function itShouldParseConsumedFunctions(): void
    {
        $code = <<<CODE
        <?php

        testfunction();
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertCount(1, $symbols);
        self::assertSame('testfunction', $symbols[0]);
    }
}

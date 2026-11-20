<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP\Strategy;

use ComposerUnused\SymbolParser\Parser\PHP\Strategy\TypedAttributeStrategy;
use ComposerUnused\SymbolParser\Test\ParserTestCase;

final class TypedAttributeStrategyTest extends ParserTestCase
{
    private TypedAttributeStrategy $stragety;

    protected function setUp(): void
    {
        $this->stragety = new TypedAttributeStrategy();
    }

    /**
     * @test
     */
    public function itShouldParseSymbolFromTypedAttribute(): void
    {
        $code = <<<CODE
        <?php

        namespace Testing;

        use Other\Fubar;

        class Foo {
            private \$foo;
            private Fubar \$baz;
            private \My\Namespace\Bar \$bar;
        }
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->stragety], $code);

        self::assertCount(2, $symbols);
        self::assertSame('Other\Fubar', $symbols[0]);
        self::assertSame('My\Namespace\Bar', $symbols[1]);
    }
}

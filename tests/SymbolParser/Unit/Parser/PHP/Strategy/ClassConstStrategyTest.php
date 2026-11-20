<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Parser\PHP\Strategy;

use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ClassConstStrategy;
use ComposerUnused\SymbolParser\Test\ParserTestCase;

/**
 * @author Laurent Laville
 */
class ClassConstStrategyTest extends ParserTestCase
{
    private ClassConstStrategy $strategy;

    protected function setUp(): void
    {
        $this->strategy = new ClassConstStrategy();
    }

    /**
     * @link https://github.com/composer-unused/symbol-parser/issues/142
     * @test
     */
    public function itShouldParseSpecialClassNames(): void
    {
        $code = <<<CODE
        <?php
        namespace My\Space;

        class Base
        {
            protected const BAR = 'bar_base';
        }

        class Foo extends Base
        {
            protected const BAR = 'bar_foo';

            public function getParentBar()
            {
                return parent::BAR;
            }

            public function getSelfBar()
            {
                return self::BAR;
            }

            public function getStaticBar()
            {
                return static::BAR;
            }

            public function getFooBar()
            {
                return Foo::BAR;
            }
        }
        CODE;

        $symbols = $this->parseConsumedSymbols([$this->strategy], $code);

        self::assertCount(3, $symbols);
        self::assertSame('My\Space\Base', $symbols[0]);
        self::assertSame('static', $symbols[1]);
        self::assertSame('My\Space\Foo', $symbols[2]);
    }
}

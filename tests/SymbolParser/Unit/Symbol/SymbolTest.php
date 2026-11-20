<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Symbol;

use ComposerUnused\SymbolParser\Symbol\Symbol;
use PHPUnit\Framework\TestCase;

class SymbolTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldMatchOtherSymbolWithSameName(): void
    {
        $symbol = new Symbol('test');

        self::assertTrue($symbol->matches(new Symbol('test')));
    }
}

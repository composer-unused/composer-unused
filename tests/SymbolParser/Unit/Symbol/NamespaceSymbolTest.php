<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Symbol;

use ComposerUnused\SymbolParser\Symbol\NamespaceSymbol;
use ComposerUnused\SymbolParser\Symbol\Symbol;
use PHPUnit\Framework\TestCase;

class NamespaceSymbolTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldNotMatchWithFunctions(): void
    {
        $functionSymbol = new Symbol('function');
        $namespaceSymbol = new NamespaceSymbol(__NAMESPACE__);

        self::assertFalse($functionSymbol->matches($namespaceSymbol));
        self::assertFalse($namespaceSymbol->matches($functionSymbol));
    }

    /**
     * @test
     */
    public function itShouldMatchNameSpaceFromClass(): void
    {
        $namespaceSymbol = new NamespaceSymbol(__NAMESPACE__);
        $namespaceSymbolFromClass = NamespaceSymbol::fromClass(self::class);

        self::assertTrue($namespaceSymbol->matches($namespaceSymbolFromClass));
    }
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Symbol;

use Icanhazstring\Composer\Unused\Symbol\NamespaceSymbol;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
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
        $namespaceSymbolFromClass = new NamespaceSymbol(self::class);

        self::assertTrue($namespaceSymbol->matches($namespaceSymbolFromClass));
    }
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Symbol;

use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolList;
use Iterator;
use PHPUnit\Framework\TestCase;

class SymbolListTest extends TestCase
{
    /**
     * @param array<mixed> $data
     * @return Iterator<mixed>
     */
    private function arrayAsIterator(array $data): Iterator
    {
        yield from $data;
    }

    /**
     * @test
     */
    public function itShouldBeImmutable(): void
    {
        $symbol = new Symbol('testsymbol');
        $list = new SymbolList();

        self::assertNotSame($list, $list->add($symbol));
    }

    /**
     * @test
     */
    public function itShouldContainAddedSymbol(): void
    {
        $symbol = new Symbol('testsymbol');
        $list = (new SymbolList())->add($symbol);

        self::assertTrue($list->contains($symbol));
    }

    /**
     * @test
     */
    public function itShouldAddListOfSymbols(): void
    {
        $symbol1 = new Symbol('testsymbol1');
        $symbol2 = new Symbol('testsymbol2');

        $list = (new SymbolList())->addAll($this->arrayAsIterator([$symbol1, $symbol2]));

        self::assertTrue($list->contains($symbol1));
        self::assertTrue($list->contains($symbol2));
    }

    /**
     * @test
     */
    public function itIteratesOverItems(): void
    {
        $symbol1 = new Symbol('testsymbol1');
        $symbol2 = new Symbol('testsymbol2');

        $list = (new SymbolList())->addAll($this->arrayAsIterator([$symbol1, $symbol2]));

        foreach ($list as $symbol) {
            self::assertContains($symbol, [$symbol1, $symbol2]);
        }
    }
}

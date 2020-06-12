<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Dependency;

use Icanhazstring\Composer\Unused\Dependency\Dependency;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolList;
use Icanhazstring\Composer\Unused\Symbol\SymbolListInterface;
use PHPUnit\Framework\TestCase;

class DependencyTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldIterateOverSymbolListsInCorrectOrder(): void
    {
        $symbol = new Symbol('test');
        $classSymbols = (new SymbolList())->add($symbol);

        $functionSymbols = $this->getMockBuilder(SymbolListInterface::class)->getMock();
        $constantSymbols = $this->getMockBuilder(SymbolListInterface::class)->getMock();

        $functionSymbols->expects($this->never())->method('contains');
        $constantSymbols->expects($this->never())->method('contains');

        $dependency = new Dependency($classSymbols, $functionSymbols, $constantSymbols);
        $dependency->provides($symbol);
    }

    /**
     * @test
     */
    public function itShouldIterateOverFunctionsAndConstantsWithFileAutoloading(): void
    {
        $symbol = new Symbol('test');
        $classSymbols = (new SymbolList())->add($symbol);

        $functionSymbols = $this->getMockBuilder(SymbolListInterface::class)->getMock();
        $constantSymbols = $this->getMockBuilder(SymbolListInterface::class)->getMock();

        $functionSymbols
            ->expects($this->once())
            ->method('contains')
            ->with($symbol)
            ->willReturn(false);
        $constantSymbols
            ->expects($this->once())
            ->method('contains')
            ->with($symbol)
            ->willReturn(false);

        $dependency = new Dependency($classSymbols, $functionSymbols, $constantSymbols);
        $dependency->withOnlyFileAutoload()->provides($symbol);
    }
}

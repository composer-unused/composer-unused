<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Symbol;

use Composer\Package\Package;
use Generator;
use Icanhazstring\Composer\Unused\Symbol\DependencySymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FunctionConstantSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use Icanhazstring\Composer\Unused\Symbol\SymbolLoaderInterface;
use PHPUnit\Framework\TestCase;

class DependencySymbolLoaderTest extends TestCase
{
    protected function arrayAsGenerator(array $values): Generator
    {
        foreach ($values as $value) {
            yield $value;
        }
    }

    private function assertContainsSymbol(SymbolInterface $symbol, array $symbolHaystack): void
    {
        foreach ($symbolHaystack as $refSymbol) {
            if ($refSymbol->matches($symbol)) {
                return;
            }
        }

        $this->fail($symbol->getIdentifier() . ' not found in haystack');
    }

    /**
     * @test
     */
    public function itShouldReturnEmptySymbolsOnEmptyPackage(): void
    {
        $package = new Package('test', '*', '*');
        $package->setTargetDir('/');
        $package->setAutoload([]);

        $symbolLoader = $this->getMockForAbstractClass(SymbolLoaderInterface::class);
        $symbolLoader->method('load')->willReturn($this->arrayAsGenerator([]));

        $fileSymbolLoader = $this->getMockBuilder(FunctionConstantSymbolProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSymbolLoader
            ->method('provide')
            ->willReturn($this->arrayAsGenerator([]));

        $symbolLoader = new DependencySymbolLoader($fileSymbolLoader, $symbolLoader);
        $symbols = $symbolLoader->load($package);

        self::assertEmpty(iterator_to_array($symbols));
    }

    /**
     * @test
     */
    public function itShouldMatchFiles(): void
    {
        $package = new Package('test', '*', '*');
        $package->setTargetDir('/foobar');
        $package->setAutoload([
            'files' => [
                'include/functions.php'
            ]
        ]);

        $symbolLoader = $this->getMockForAbstractClass(SymbolLoaderInterface::class);
        $symbolLoader->method('load')->willReturn($this->arrayAsGenerator([]));

        $fileSymbolProvider = $this->getMockBuilder(FunctionConstantSymbolProvider::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSymbolProvider
            ->expects($this->once())
            ->method('provide')
            ->with('foobar', ['include/functions.php'])
            ->willReturn($this->arrayAsGenerator([
                new Symbol('testfunction')
            ]));

        $symbolLoader = new DependencySymbolLoader($fileSymbolProvider, $symbolLoader);
        $symbols = $symbolLoader->load($package);

        $symbolsArray = iterator_to_array($symbols);
        self::assertCount(1, $symbolsArray);
        $this->assertContainsSymbol(new Symbol('testfunction'), $symbolsArray);
    }
}

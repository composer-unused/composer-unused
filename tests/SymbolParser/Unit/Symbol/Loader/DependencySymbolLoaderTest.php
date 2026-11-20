<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Unit\Symbol\Loader;

use ComposerUnused\SymbolParser\Symbol\Loader\CompositeSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\SymbolLoaderInterface;
use ComposerUnused\SymbolParser\Symbol\Symbol;
use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use ComposerUnused\SymbolParser\Test\Stubs\TestPackage;
use Generator;
use PHPUnit\Framework\TestCase;

class DependencySymbolLoaderTest extends TestCase
{
    /**
     * @param array<mixed> $values
     * @return Generator<mixed>
     */
    protected function arrayAsGenerator(array $values): Generator
    {
        yield from $values;
    }

    /**
     * @param array<SymbolInterface> $symbolHaystack
     */
    private function assertContainsSymbol(SymbolInterface $symbol, array $symbolHaystack): void
    {
        foreach ($symbolHaystack as $refSymbol) {
            if ($refSymbol->matches($symbol)) {
                return;
            }
        }

        self::fail($symbol->getIdentifier() . ' not found in haystack');
    }

    /**
     * @test
     */
    public function itShouldReturnEmptySymbolsOnEmptyPackage(): void
    {
        $package = new TestPackage();
        $package->name = 'test';

        $firstSymbolLoader = $this->getMockForAbstractClass(SymbolLoaderInterface::class);
        $firstSymbolLoader->method('load')->willReturn($this->arrayAsGenerator([]));

        $secondSymbolLoader = $this->getMockForAbstractClass(SymbolLoaderInterface::class);
        $secondSymbolLoader->method('load')->willReturn($this->arrayAsGenerator([]));

        $firstSymbolLoader = new CompositeSymbolLoader([$secondSymbolLoader, $firstSymbolLoader]);
        $symbols = $firstSymbolLoader->load($package);

        self::assertEmpty(iterator_to_array($symbols));
    }

    /**
     * @test
     */
    public function itShouldMatchFiles(): void
    {
        $package = new TestPackage();
        $package->name = 'test';
        $package->autoload = [
            'files' => [
                'include/functions.php'
            ]
        ];

        $firstSymbolLoader = $this->getMockForAbstractClass(SymbolLoaderInterface::class);
        $firstSymbolLoader->method('load')->willReturn($this->arrayAsGenerator([]));
        $firstSymbolLoader->method('withBaseDir')->willReturnSelf();

        $secondSymbolLoader = $this->getMockForAbstractClass(SymbolLoaderInterface::class);
        $secondSymbolLoader->method('withBaseDir')->willReturnSelf();
        $secondSymbolLoader
            ->expects(self::once())
            ->method('load')
            ->willReturn($this->arrayAsGenerator([
                new Symbol('testfunction')
            ]));

        $compositeSymbolLoader = new CompositeSymbolLoader([$secondSymbolLoader, $firstSymbolLoader]);
        $symbols = $compositeSymbolLoader->load($package);

        $symbolsArray = iterator_to_array($symbols);
        self::assertCount(1, $symbolsArray);
        $this->assertContainsSymbol(new Symbol('testfunction'), $symbolsArray);
    }
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Symbol\Loader;

use Composer\Package\Package;
use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Symbol\Loader\CompositeSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\SymbolLoaderInterface;
use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
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
        $package = new Package('test', '*', '*');
        $package->setTargetDir('/');
        $package->setAutoload([]);

        $symbolLoader = $this->getMockForAbstractClass(SymbolLoaderInterface::class);
        $symbolLoader->method('load')->willReturn($this->arrayAsGenerator([]));

        $fileSymbolLoader = $this->getMockBuilder(FileSymbolLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSymbolLoader
            ->method('load')
            ->willReturn($this->arrayAsGenerator([]));

        $symbolLoader = new CompositeSymbolLoader([$fileSymbolLoader, $symbolLoader]);
        $symbols = $symbolLoader->load(PackageDecorator::withBaseDir('', $package));

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

        $fileSymbolProvider = $this->getMockBuilder(FileSymbolLoader::class)
            ->disableOriginalConstructor()
            ->getMock();

        $fileSymbolProvider
            ->expects($this->once())
            ->method('load')
            ->willReturn($this->arrayAsGenerator([
                new Symbol('testfunction')
            ]));

        $symbolLoader = new CompositeSymbolLoader([$fileSymbolProvider, $symbolLoader]);
        $symbols = $symbolLoader->load(PackageDecorator::withBaseDir('', $package));

        $symbolsArray = iterator_to_array($symbols);
        self::assertCount(1, $symbolsArray);
        $this->assertContainsSymbol(new Symbol('testfunction'), $symbolsArray);
    }
}

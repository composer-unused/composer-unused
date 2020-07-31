<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Unit\Symbol;

use Composer\Package\Package;
use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Symbol\Loader\CompositeSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\PsrSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\NamespaceSymbol;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use PHPUnit\Framework\TestCase;

class SymbolLoaderTest extends TestCase
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
    public function itShouldMatchPsr0(): void
    {
        $package = new Package('test', '*', '*');
        $package->setTargetDir('/');
        $package->setAutoload([
            'psr-0' => [
                'Test\\Namespace\\' => 'src'
            ]
        ]);

        $symbolLoader = new CompositeSymbolLoader([new PsrSymbolLoader()]);
        $symbols = $symbolLoader->load(PackageDecorator::withBaseDir('', $package));

        $symbolsArray = iterator_to_array($symbols);
        self::assertCount(1, $symbolsArray);
        $this->assertContainsSymbol(new NamespaceSymbol('Test\\Namespace\\'), $symbolsArray);
    }

    /**
     * @test
     */
    public function itShouldMatchPsr4(): void
    {
        $package = new Package('test', '*', '*');
        $package->setTargetDir('/');
        $package->setAutoload([
            'psr-4' => [
                'Test\\Namespace\\' => 'src'
            ]
        ]);

        $symbolLoader = new CompositeSymbolLoader([new PsrSymbolLoader()]);
        $symbols = $symbolLoader->load(PackageDecorator::withBaseDir('', $package));

        $symbolsArray = iterator_to_array($symbols);
        self::assertCount(1, $symbolsArray);
        $this->assertContainsSymbol(new NamespaceSymbol('Test\\Namespace\\'), $symbolsArray);
    }
}

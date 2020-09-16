<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Symbol\Loader;

use Icanhazstring\Composer\Test\Unused\Integration\AbstractIntegrationTestCase;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Loader\UsedSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use PhpParser\ParserFactory;

use function iterator_to_array;

class UsedSymbolLoaderTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     */
    public function itFindsLocalFileSymbols(): void
    {
        $baseDir = __DIR__ . '/../../../assets/TestProjects/ClassmapAutoload';

        $package = $this->getComposer($baseDir)->getPackage();
        $package = PackageDecorator::withBaseDir($baseDir, $package);

        $usedSymbolLoader = $this->createSymbolLoader();

        /** @var array<SymbolInterface> $symbols */
        $symbols = iterator_to_array($usedSymbolLoader->load($package));

        self::assertCount(1, $symbols);
    }

    private function createSymbolLoader(): UsedSymbolLoader
    {
        return new UsedSymbolLoader(
            new FileSymbolProvider(
                new SymbolNameParser(
                    (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                    new ForeignSymbolCollector()
                ),
                new FileContentProvider()
            )
        );
    }
}

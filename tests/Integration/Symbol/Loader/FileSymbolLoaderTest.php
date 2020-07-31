<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Symbol\Loader;

use Icanhazstring\Composer\Test\Unused\Integration\AbstractIntegrationTestCase;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use PhpParser\ParserFactory;

use function dirname;
use function iterator_to_array;

class FileSymbolLoaderTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     */
    public function itFindsForeignFileSymbols(): void
    {
        $package = $this->loadPackage(__DIR__ . '/../../../assets/TestProjects/OnlyFileDependencies', 'test/file-dependency');
        $fileLoader = $this->createFileSymbolLoader();

        /** @var array<SymbolInterface> $symbols */
        $symbols = iterator_to_array($fileLoader->load($package));

        self::assertCount(1, $symbols);
        self::assertEquals('testfunction', $symbols['testfunction']->getIdentifier());
    }

    private function createFileSymbolLoader(): FileSymbolLoader
    {
        return new FileSymbolLoader(
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

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\UseCase;

use Icanhazstring\Composer\Test\Unused\Integration\AbstractIntegrationTestCase;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Loader\UsedSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use PhpParser\ParserFactory;

use function iterator_to_array;

final class CollectUsedSymbolsUseCaseTest extends AbstractIntegrationTestCase
{
    /**
     * @test
     */
    public function itFindsLocalFileSymbols(): void
    {
        $baseDir = __DIR__ . '/../../assets/TestProjects/ClassmapAutoload';

        $package = $this->getComposer($baseDir)->getPackage();

        $useCase = $this->createUseCase();

        /** @var array<SymbolInterface> $symbols */
        $symbols = iterator_to_array($useCase->execute($package, $baseDir));

        self::assertCount(1, $symbols);
    }

    private function createUseCase(): CollectUsedSymbolsUseCase
    {
        return new CollectUsedSymbolsUseCase(
            new UsedSymbolLoader(
                new FileSymbolProvider(
                    new SymbolNameParser(
                        (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                        new ForeignSymbolCollector()
                    ),
                    new FileContentProvider()
                )
            )
        );
    }
}

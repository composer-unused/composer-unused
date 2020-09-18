<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Symbol\Loader;

use Composer\Package\RootPackageInterface;
use Generator;
use Icanhazstring\Composer\Test\Unused\Integration\AbstractIntegrationTestCase;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\UsedSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\Symbol\SymbolInterface;
use PhpParser\ParserFactory;

use function array_merge;
use function iterator_to_array;

class ClassmapAutoloadTest extends AbstractIntegrationTestCase
{
    private const BASE_DIR = __DIR__ . '/../../../assets/TestProjects/ClassmapAutoload';

    /**
     * @test
     */
    public function itShouldLoadRootSymbolsCorrectly(): void
    {
        $rootPackage = $this->loadRootPackage();
        $package = PackageDecorator::withBaseDir(
            self::BASE_DIR,
            $rootPackage
        );

        /** @var array<SymbolInterface> $symbols */
        $symbols = iterator_to_array($this->collectUsedSymbols($package));

        self::assertCount(1, $symbols);
    }

    /**
     * @test
     */
    public function itShouldLoadForeignSymbolsCorrectly(): void
    {
        $rootPackage = $this->loadRootPackage();
        $requiredSymbols = [];

        foreach ($rootPackage->getRequires() as $require) {
            $composerPackage = $rootPackage->getRepository()->findPackage(
                $require->getTarget(),
                $require->getConstraint()
            );

            /** @phpstan-ignore-next-line */
            $package = PackageDecorator::withBaseDir(
                self::BASE_DIR,
                $composerPackage
            );

            $requiredSymbols[] = iterator_to_array($this->collectRequiredSymbols($package));
        }

        $requiredSymbols = array_merge(...$requiredSymbols);
        self::assertCount(2, $requiredSymbols);
    }

    /**
     * @return Generator<SymbolInterface>
     */
    private function collectUsedSymbols(PackageDecoratorInterface $package): Generator
    {
        return (new UsedSymbolLoader(
            new FileSymbolProvider(
                new SymbolNameParser(
                    (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                    new ForeignSymbolCollector()
                ),
                new FileContentProvider()
            )
        ))->load($package);
    }

    /**
     * @return Generator<SymbolInterface>
     */
    private function collectRequiredSymbols(PackageDecoratorInterface $package): Generator
    {
        return (new FileSymbolLoader(
            new FileSymbolProvider(
                new SymbolNameParser(
                    (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                    new ForeignSymbolCollector()
                ),
                new FileContentProvider()
            )
        ))->load($package);
    }

    private function loadRootPackage(): RootPackageInterface
    {
        $composer = $this->getComposer(self::BASE_DIR);
        $rootPackage = $composer->getPackage();

        $rootPackage->setRepository(
            $composer->getRepositoryManager()->getLocalRepository()
        );

        return $rootPackage;
    }
}

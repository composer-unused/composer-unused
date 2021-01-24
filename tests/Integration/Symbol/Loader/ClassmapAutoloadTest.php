<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Test\Unused\Integration\Symbol\Loader;

use ClassmapAutoload\Addon\Parsed\Lib\ParsedClass;
use ClassmapAutoload\Redis\MyRedis;
use Composer\Package\RootPackageInterface;
use Generator;
use Icanhazstring\Composer\Test\Unused\Integration\AbstractIntegrationTestCase;
use Icanhazstring\Composer\Unused\Composer\PackageDecorator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
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
        $symbols = iterator_to_array($this->collectSymbols($package));

        self::assertCount(3, $symbols);
        self::assertArrayHasKey('ClassmapAutoload\Addon\Parsed\Lib\ParsedClass', $symbols);
        self::assertArrayHasKey('ClassmapAutoload\ParsedClass', $symbols);
        self::assertArrayHasKey('ClassmapAutoload\Redis\MyRedis', $symbols);
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

            if ($composerPackage === null) {
                continue;
            }

            $package = PackageDecorator::withBaseDir(
                self::BASE_DIR . '/vendor/' . $require->getTarget(),
                $composerPackage
            );

            $requiredSymbols[] = iterator_to_array($this->collectSymbols($package));
        }

        $requiredSymbols = array_merge(...$requiredSymbols);
        self::assertCount(2, $requiredSymbols);
    }

    /**
     * @return Generator<SymbolInterface>
     */
    private function collectSymbols(PackageDecoratorInterface $package): Generator
    {
        return (new FileSymbolLoader(
            new FileSymbolProvider(
                new SymbolNameParser(
                    (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                    new ForeignSymbolCollector()
                ),
                new FileContentProvider()
            ),
            ['classmap']
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

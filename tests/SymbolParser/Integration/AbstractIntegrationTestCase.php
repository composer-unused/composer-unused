<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Integration;

use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\AutoloadType;
use ComposerUnused\SymbolParser\Parser\PHP\ConsumedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\DefinedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ClassConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\FunctionInvocationStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\NewStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StaticStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UsedExtensionSymbolStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use ComposerUnused\SymbolParser\Symbol\SymbolInterface;
use ComposerUnused\SymbolParser\Test\Stubs\Config;
use ComposerUnused\SymbolParser\Test\Stubs\TestLink;
use ComposerUnused\SymbolParser\Test\Stubs\TestPackage;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\ParserFactory;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

class AbstractIntegrationTestCase extends TestCase
{
    private function getSerializer(): SerializerInterface
    {
        return new Serializer([new PropertyNormalizer()], [new JsonEncoder()]);
    }

    private function loadVendorPackage(string $cwd, string $packageName): PackageInterface
    {
        return $this->loadPackage($cwd . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $packageName);
    }

    /**
     * @param list<string> $autoloadTypes
     * @return array<SymbolInterface>
     */
    protected function loadDefinedFileSymbols(
        string $baseDir,
        ?array $autoloadTypes = null,
        ?string $packageName = null
    ): array {
        if ($packageName === null) {
            $package = $this->loadPackage($baseDir);
        } else {
            $package = $this->loadVendorPackage($baseDir, $packageName);
            $baseDir .= '/vendor/' . $package->getName();
        }

        if ($autoloadTypes === null) {
            $autoloadTypes = AutoloadType::all();
        }

        $fileLoader = new FileSymbolLoader(
            new FileSymbolProvider(
                new SymbolNameParser(
                    (new ParserFactory())->createForNewestSupportedVersion(),
                    new NameResolver(),
                    new DefinedSymbolCollector()
                ),
                new FileContentProvider()
            ),
            $autoloadTypes
        );

        return iterator_to_array(
            $fileLoader->withBaseDir($baseDir)->load($package)
        );
    }

    /**
     * @param list<string> $autoloadTypes
     * @return array<SymbolInterface>
     * @param list<string> $excludedDirs
     */
    protected function loadConsumedFileSymbols(string $baseDir, ?array $autoloadTypes = null, array $excludedDirs = []): array
    {
        $rootPackage = $this->loadPackage($baseDir);

        if ($autoloadTypes === null) {
            $autoloadTypes = AutoloadType::all();
        }

        $fileLoader = new FileSymbolLoader(
            new FileSymbolProvider(
                new SymbolNameParser(
                    (new ParserFactory())->createForNewestSupportedVersion(),
                    new NameResolver(),
                    new ConsumedSymbolCollector(
                        [
                            new NewStrategy(),
                            new StaticStrategy(),
                            new UseStrategy(),
                            new ClassConstStrategy(),
                            new UsedExtensionSymbolStrategy(
                                get_loaded_extensions(),
                                new NullLogger()
                            ),
                            new FunctionInvocationStrategy()
                        ]
                    )
                ),
                new FileContentProvider()
            ),
            $autoloadTypes,
            $excludedDirs
        );

        return iterator_to_array(
            $fileLoader->withBaseDir($baseDir)->load($rootPackage)
        );
    }

    protected function loadPackage(string $baseDir): PackageInterface
    {
        $composerJson = $baseDir . DIRECTORY_SEPARATOR . 'composer.json';
        $jsonContent = file_get_contents($composerJson);
        /** @var Config $config */
        $config = $this->getSerializer()->deserialize($jsonContent, Config::class, 'json');

        $package = new TestPackage();
        $package->requires = array_map(static function (string $require): LinkInterface {
            $link = new TestLink();
            $link->target = $require;

            return $link;
        }, array_keys($config->require));
        $package->autoload = $config->autoload;
        $package->name = $config->name;

        return $package;
    }
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase\Factory;

use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\DefinedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Loader\CompositeSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\ExtensionSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\FileSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\PsrSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\PackageResolver;
use Icanhazstring\Composer\Unused\UseCase\CollectRequiredDependenciesUseCase;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;

class CollectRequiredDependenciesUseCaseFactory
{
    public function __invoke(ContainerInterface $container): CollectRequiredDependenciesUseCase
    {
        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            new DefinedSymbolCollector()
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        $dependencySymbolLoader = new CompositeSymbolLoader(
            [
                $container->get(ExtensionSymbolLoader::class),
                $container->get(PsrSymbolLoader::class),
                new FileSymbolLoader($fileSymbolProvider, ['classmap', 'files'])
            ]
        );

        return new CollectRequiredDependenciesUseCase(
            $dependencySymbolLoader,
            new PackageResolver()
        );
    }
}

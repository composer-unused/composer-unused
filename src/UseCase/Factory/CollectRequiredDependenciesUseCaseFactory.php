<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase\Factory;

use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Loader\CompositeSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\ExtensionSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\PsrSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\UseCase\CollectRequiredDependenciesUseCase;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;

class CollectRequiredDependenciesUseCaseFactory
{
    public function __invoke(ContainerInterface $container): CollectRequiredDependenciesUseCase
    {
        return new CollectRequiredDependenciesUseCase(
            new CompositeSymbolLoader(
                [
                    $container->get(ExtensionSymbolLoader::class),
                    $container->get(PsrSymbolLoader::class),
                    new FileSymbolLoader(
                        new FileSymbolProvider(
                            new SymbolNameParser(
                                (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                                new ForeignSymbolCollector()
                            ),
                            new FileContentProvider()
                        )
                    )
                ]
            )
        );
    }
}

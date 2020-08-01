<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Factory;

use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Output\SymfonyStyleFactory;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ClassConstStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\NewStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\PhpExtensionStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\StaticStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\UseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Parser\PHP\UsedSymbolCollector;
use Icanhazstring\Composer\Unused\Symbol\Loader\CompositeSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\ExtensionSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\PsrSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\UsedSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function get_loaded_extensions;

class UnusedCommandFactory
{
    public function __invoke(ContainerInterface $container): UnusedCommand
    {
        return new UnusedCommand(
            $container->get(ErrorHandlerInterface::class),
            new SymfonyStyleFactory(),
            $container->get(LoaderBuilder::class),
            $container->get(LoggerInterface::class),
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
            ),
            $container->get(CollectUsedSymbolsUseCase::class)
        );
    }
}

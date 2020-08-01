<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase\Factory;

use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ClassConstStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\NewStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\PhpExtensionStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\StaticStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\UseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Parser\PHP\UsedSymbolCollector;
use Icanhazstring\Composer\Unused\Symbol\Loader\UsedSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function get_loaded_extensions;

class CollectUsedSymbolsUseCaseFactory
{
    public function __invoke(ContainerInterface $container): CollectUsedSymbolsUseCase
    {
        return new CollectUsedSymbolsUseCase(
            new UsedSymbolLoader(
                new FileSymbolProvider(
                    new SymbolNameParser(
                        (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
                        new UsedSymbolCollector(
                            [
                                new NewStrategy(),
                                new StaticStrategy(),
                                new UseStrategy(),
                                new ClassConstStrategy(),
                                new PhpExtensionStrategy(
                                    get_loaded_extensions(),
                                    $container->get(LoggerInterface::class)
                                )
                            ]
                        )
                    ),
                    new FileContentProvider()
                )
            )
        );
    }
}

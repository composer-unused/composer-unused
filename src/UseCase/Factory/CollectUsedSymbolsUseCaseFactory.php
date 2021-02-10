<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\UseCase\Factory;

use ComposerUnused\SymbolParser\Parser\PHP\ConsumedSymbolCollector;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ClassConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\NewStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StaticStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UsedExtensionSymbolStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UseStrategy;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function get_loaded_extensions;

class CollectUsedSymbolsUseCaseFactory
{
    public function __invoke(ContainerInterface $container): CollectUsedSymbolsUseCase
    {
        $usedSymbolCollector = new ConsumedSymbolCollector(
            [
                new NewStrategy(),
                new StaticStrategy(),
                new UseStrategy(),
                new ClassConstStrategy(),
                new UsedExtensionSymbolStrategy(
                    get_loaded_extensions(),
                    $container->get(LoggerInterface::class)
                )
            ]
        );

        $symbolNameParser = new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            $usedSymbolCollector
        );

        $fileSymbolProvider = new FileSymbolProvider(
            $symbolNameParser,
            new FileContentProvider()
        );

        return new CollectUsedSymbolsUseCase(
            new FileSymbolLoader(
                $fileSymbolProvider,
                ['classmap', 'files', 'psr-0', 'psr-4']
            )
        );
    }
}

<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Command\Handler\Factory;

use ComposerUnused\ComposerUnused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use ComposerUnused\ComposerUnused\Symbol\ConsumedSymbolLoaderBuilder;
use Psr\Container\ContainerInterface;

final class CollectConsumedSymbolsCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): CollectConsumedSymbolsCommandHandler
    {
        return new CollectConsumedSymbolsCommandHandler($container->get(ConsumedSymbolLoaderBuilder::class));
    }
}

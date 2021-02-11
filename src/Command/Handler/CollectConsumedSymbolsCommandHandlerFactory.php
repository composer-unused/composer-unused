<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Command\Handler;

use Psr\Container\ContainerInterface;

final class CollectConsumedSymbolsCommandHandlerFactory
{
    public function __invoke(ContainerInterface $container): CollectConsumedSymbolsCommandHandler
    {
        return new CollectConsumedSymbolsCommandHandler($container->get(ConsumedSymbolLoaderBuilder::class));
    }
}

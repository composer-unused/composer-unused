<?php

declare(strict_types=1);

use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandler;
use Icanhazstring\Composer\Unused\Command\Handler\CollectConsumedSymbolsCommandHandlerFactory;
use Icanhazstring\Composer\Unused\Command\Handler\ConsumedSymbolLoaderBuilder;
use Icanhazstring\Composer\Unused\Console\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Console\Command\UnusedCommandFactory;
use Icanhazstring\Composer\Unused\Di\InvokableFactory;

return [
    'factories' => [
        UnusedCommand::class => UnusedCommandFactory::class,
        CollectConsumedSymbolsCommandHandler::class => CollectConsumedSymbolsCommandHandlerFactory::class,
        ConsumedSymbolLoaderBuilder::class => InvokableFactory::class
    ]
];

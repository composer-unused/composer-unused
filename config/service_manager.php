<?php

declare(strict_types=1);

use Icanhazstring\Composer\Unused\Command\Factory\UnusedCommandFactory;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Di\InvokableFactory;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Error\Factory\ErrorHandlerFactory;
use Icanhazstring\Composer\Unused\Event\Factory\EventDispatcherFactory;
use Icanhazstring\Composer\Unused\Event\Listener\Factory\RuntimeListenerProviderFactory;
use Icanhazstring\Composer\Unused\Event\Listener\RuntimeListenerProvider;
use Icanhazstring\Composer\Unused\Event\ListenerEventTypeResolver;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Loader\Factory\LoaderBuilderFactory;
use Icanhazstring\Composer\Unused\Loader\Factory\PackageLoaderFactory;
use Icanhazstring\Composer\Unused\Loader\Factory\UsageLoaderFactory;
use Icanhazstring\Composer\Unused\Loader\LoaderBuilder;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Log\Factory\DebugLoggerFactory;
use Icanhazstring\Composer\Unused\Log\Factory\FileHandlerFactory;
use Icanhazstring\Composer\Unused\Log\LogHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\Factory\NodeVisitorFactory;
use Icanhazstring\Composer\Unused\Parser\PHP\Factory\PHPUsageParserFactory;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\NamespaceNodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\PHPUsageParser;
use Icanhazstring\Composer\Unused\Parser\PHP\UsedSymbolCollector;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use ComposerUnused\SymbolParser\Symbol\Loader\ExtensionSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Loader\PsrSymbolLoader;
use ComposerUnused\SymbolParser\Symbol\Provider\Factory\FileSymbolProviderFactory;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use Icanhazstring\Composer\Unused\UseCase\CollectRequiredDependenciesUseCase;
use Icanhazstring\Composer\Unused\UseCase\CollectUsedSymbolsUseCase;
use Icanhazstring\Composer\Unused\UseCase\Factory\CollectRequiredDependenciesUseCaseFactory;
use Icanhazstring\Composer\Unused\UseCase\Factory\CollectUsedSymbolsUseCaseFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Log\LoggerInterface;

return [
    'factories' => [
        NamespaceNodeVisitor::class => NodeVisitorFactory::class,
        UsageLoader::class => UsageLoaderFactory::class,
        PackageLoader::class => PackageLoaderFactory::class,
        PackageSubjectFactory::class => static function () {
            return new PackageSubjectFactory();
        },
        ErrorHandlerInterface::class => ErrorHandlerFactory::class,
        UnusedCommand::class => UnusedCommandFactory::class,
        LoggerInterface::class => DebugLoggerFactory::class,
        LogHandlerInterface::class => FileHandlerFactory::class,
        LoaderBuilder::class => LoaderBuilderFactory::class,
        PHPUsageParser::class => PHPUsageParserFactory::class,

        // 0.8 dependencies
        ExtensionSymbolLoader::class => InvokableFactory::class,
        PsrSymbolLoader::class => InvokableFactory::class,
        FileSymbolProvider::class => FileSymbolProviderFactory::class,
        FileContentProvider::class => InvokableFactory::class,
        ForeignSymbolCollector::class => InvokableFactory::class,
        UsedSymbolCollector::class => InvokableFactory::class,
        CollectUsedSymbolsUseCase::class => CollectUsedSymbolsUseCaseFactory::class,
        CollectRequiredDependenciesUseCase::class => CollectRequiredDependenciesUseCaseFactory::class,

        ListenerEventTypeResolver::class => InvokableFactory::class,
        RuntimeListenerProvider::class => RuntimeListenerProviderFactory::class,
        EventDispatcherInterface::class => EventDispatcherFactory::class,

    ]
];

<?php

declare(strict_types=1);

use Icanhazstring\Composer\Unused\Command\Factory\UnusedCommandFactory;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Di\InvokableFactory;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Error\Factory\ErrorHandlerFactory;
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
use Icanhazstring\Composer\Unused\Parser\PHP\Factory\ForeignSymbolNameParserFactory;
use Icanhazstring\Composer\Unused\Parser\PHP\Factory\NodeVisitorFactory;
use Icanhazstring\Composer\Unused\Parser\PHP\Factory\PHPUsageParserFactory;
use Icanhazstring\Composer\Unused\Parser\PHP\Factory\SymbolNameParserFactory;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolNameParserInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\NamespaceNodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\PHPUsageParser;
use Icanhazstring\Composer\Unused\Parser\PHP\UsedSymbolCollector;
use Icanhazstring\Composer\Unused\Parser\PHP\RootSymbolNameParserInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Parser\PHP\ForeignSymbolCollector;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Icanhazstring\Composer\Unused\Symbol\Loader\ExtensionSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\Factory\FileSymbolLoaderFactory;
use Icanhazstring\Composer\Unused\Symbol\Loader\FileSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Loader\PsrSymbolLoader;
use Icanhazstring\Composer\Unused\Symbol\Provider\Factory\FileSymbolProviderFactory;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
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
        //FileSymbolLoader::class => FileSymbolLoaderFactory::class,
        FileSymbolProvider::class => FileSymbolProviderFactory::class,
        FileContentProvider::class => InvokableFactory::class,
        ForeignSymbolCollector::class => InvokableFactory::class,
        UsedSymbolCollector::class => InvokableFactory::class,
    ]
];

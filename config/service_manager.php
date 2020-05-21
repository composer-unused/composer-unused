<?php

declare(strict_types=1);

use Icanhazstring\Composer\Unused\Command\Factory\UnusedCommandFactory;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Error\Factory\ErrorHandlerFactory;
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
use Icanhazstring\Composer\Unused\Parser\PHP\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\PHPUsageParser;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Psr\Log\LoggerInterface;

return [
    'factories' => [
        NodeVisitor::class => NodeVisitorFactory::class,
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
        PHPUsageParser::class => PHPUsageParserFactory::class
    ]
];

<?php

declare(strict_types=1);

use Icanhazstring\Composer\Unused\Command\Factory\UnusedCommandFactory;
use Icanhazstring\Composer\Unused\Command\UnusedCommand;
use Icanhazstring\Composer\Unused\Error\ErrorDumperInterface;
use Icanhazstring\Composer\Unused\Error\Factory\ErrorDumperFactory;
use Icanhazstring\Composer\Unused\Error\Factory\FileDumperFactory;
use Icanhazstring\Composer\Unused\Error\FileDumper;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Error\Handler\Factory\ErrorHandlerFactory;
use Icanhazstring\Composer\Unused\Loader\Factory\PackageLoaderFactory;
use Icanhazstring\Composer\Unused\Loader\Factory\UsageLoaderFactory;
use Icanhazstring\Composer\Unused\Loader\PackageLoader;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Log\DebugLogger;
use Icanhazstring\Composer\Unused\Parser\Factory\NodeVisitorFactory;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Zend\ServiceManager\Factory\InvokableFactory;

return [
    'factories' => [
        NodeVisitor::class           => NodeVisitorFactory::class,
        UsageLoader::class           => UsageLoaderFactory::class,
        PackageLoader::class         => PackageLoaderFactory::class,
        PackageSubjectFactory::class => InvokableFactory::class,
        FileDumper::class            => FileDumperFactory::class,
        ErrorDumperInterface::class  => ErrorDumperFactory::class,
        ErrorHandlerInterface::class => ErrorHandlerFactory::class,
        UnusedCommand::class         => UnusedCommandFactory::class,
        DebugLogger::class           => InvokableFactory::class
    ]
];

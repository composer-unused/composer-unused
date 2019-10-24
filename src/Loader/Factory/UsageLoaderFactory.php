<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Icanhazstring\Composer\Unused\Di\FactoryInterface;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class UsageLoaderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, array $options = null): UsageLoader
    {
        return new UsageLoader(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            $container->get(NodeVisitor::class),
            $container->get(ErrorHandlerInterface::class),
            $container->get(LoggerInterface::class),
            new Result(),
            $options['excludes'] ?? []
        );
    }
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;

class UsageLoaderFactory
{
    public function __invoke(ContainerInterface $container): UsageLoader
    {
        return new UsageLoader(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            $container->get(NodeVisitor::class),
            $container->get(ErrorHandlerInterface::class)
        );
    }
}

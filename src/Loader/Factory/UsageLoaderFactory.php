<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader\Factory;

use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Loader\Result;
use Icanhazstring\Composer\Unused\Loader\UsageLoader;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Interop\Container\ContainerInterface;
use PhpParser\ParserFactory;
use Psr\Log\LoggerInterface;
use Zend\ServiceManager\Factory\FactoryInterface;

class UsageLoaderFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, $requestedName, array $options = null): UsageLoader
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

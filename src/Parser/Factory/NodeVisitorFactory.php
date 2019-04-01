<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\Factory;

use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\Strategy\ClassConstStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\NewParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\StaticParseStrategy;
use Icanhazstring\Composer\Unused\Parser\Strategy\UseParseStrategy;
use Psr\Container\ContainerInterface;

class NodeVisitorFactory
{
    public function __invoke(ContainerInterface $container): NodeVisitor
    {
        return new NodeVisitor([
            new NewParseStrategy(),
            new StaticParseStrategy(),
            new UseParseStrategy(),
            new ClassConstStrategy()
        ], $container->get(ErrorHandlerInterface::class));
    }
}

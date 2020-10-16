<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Factory;

use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\NodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ClassConstStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ExtendsParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ImplementsParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\InstanceofStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\NewParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\PhpExtensionStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\StaticParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\UseParseStrategy;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class NodeVisitorFactory
{
    public function __invoke(ContainerInterface $container): NodeVisitor
    {
        return new NodeVisitor([
            new NewParseStrategy(),
            new StaticParseStrategy(),
            new UseParseStrategy(),
            new ClassConstStrategy(),
            new PhpExtensionStrategy(
                get_loaded_extensions(),
                $container->get(LoggerInterface::class)
            ),
            new ExtendsParseStrategy(),
            new ImplementsParseStrategy(),
            new InstanceofStrategy(),
        ], $container->get(ErrorHandlerInterface::class));
    }
}

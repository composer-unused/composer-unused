<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Factory;

use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\NamespaceNodeVisitor;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ClassConstStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ExtendsParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\ImplementsParseStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\InstanceofStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\NewStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\PhpExtensionStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\StaticStrategy;
use Icanhazstring\Composer\Unused\Parser\PHP\Strategy\UseStrategy;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

class NodeVisitorFactory
{
    public function __invoke(ContainerInterface $container): NamespaceNodeVisitor
    {
        return new NamespaceNodeVisitor([
            new NewStrategy(),
            new StaticStrategy(),
            new UseStrategy(),
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

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Factory;

use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ClassConstStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ExtendsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\ImplementsParseStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\InstanceofStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\NewStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\PhpExtensionStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\StaticStrategy;
use ComposerUnused\SymbolParser\Parser\PHP\Strategy\UseStrategy;
use Icanhazstring\Composer\Unused\Error\ErrorHandlerInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\NamespaceNodeVisitor;
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

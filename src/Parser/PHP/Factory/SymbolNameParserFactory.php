<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser\PHP\Factory;

use Icanhazstring\Composer\Unused\Di\FactoryInterface;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNodeVisitor;
use PhpParser\ParserFactory;
use Psr\Container\ContainerInterface;

class SymbolNameParserFactory implements FactoryInterface
{
    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null)
    {
        return new SymbolNameParser(
            (new ParserFactory())->create(ParserFactory::ONLY_PHP7),
            $container->get(SymbolNodeVisitor::class)
        );
    }
}

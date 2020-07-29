<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Provider\Factory;

use Icanhazstring\Composer\Unused\Di\FactoryInterface;
use Icanhazstring\Composer\Unused\File\FileContentProvider;
use Icanhazstring\Composer\Unused\Parser\PHP\SymbolNameParser;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use Psr\Container\ContainerInterface;

class FileSymbolProviderFactory implements FactoryInterface
{

    public function __invoke(ContainerInterface $container, string $requestedName, array $options = null)
    {
        return new FileSymbolProvider(
            $container->get(SymbolNameParser::class),
            $container->get(FileContentProvider::class)
        );
    }
}

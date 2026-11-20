<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol\Provider\Factory;

use ComposerUnused\SymbolParser\File\FileContentProvider;
use ComposerUnused\SymbolParser\Parser\PHP\SymbolNameParser;
use ComposerUnused\SymbolParser\Symbol\Provider\FileSymbolProvider;
use Psr\Container\ContainerInterface;

class FileSymbolProviderFactory
{
    public function __invoke(ContainerInterface $container): FileSymbolProvider
    {
        return new FileSymbolProvider(
            $container->get(SymbolNameParser::class),
            $container->get(FileContentProvider::class)
        );
    }
}

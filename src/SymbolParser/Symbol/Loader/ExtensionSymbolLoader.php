<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol\Loader;

use ComposerUnused\Contracts\PackageInterface;
use Generator;
use ComposerUnused\SymbolParser\Symbol\Symbol;
use ReflectionException;
use ReflectionExtension;

final class ExtensionSymbolLoader implements SymbolLoaderInterface
{
    private const EXTENSION_ALIAS = [
        'zend-opcache' => 'Zend OPcache',
        'php' => 'Core',
        'php-64bit' => 'Core'
    ];

    public function load(PackageInterface $package): Generator
    {
        if (!$this->isExtension($package)) {
            return [];
        }

        $packageName = str_replace('ext-', '', $package->getName());

        try {
            $reflection = new ReflectionExtension(
                self::EXTENSION_ALIAS[$packageName] ?? $packageName
            );
        } catch (ReflectionException $e) {
            return [];
        }

        $symbolNames = array_merge(
            array_flip($reflection->getClassNames()),
            $reflection->getConstants(),
            $reflection->getFunctions()
        );

        yield from array_map(static function (string $symbolName) {
            return new Symbol($symbolName);
        }, array_keys($symbolNames));
    }

    private function isExtension(PackageInterface $package): bool
    {
        return strpos($package->getName(), 'ext-') === 0
               || $package->getName() === 'php'
               || $package->getName() === 'php-64bit';
    }

    public function withBaseDir(?string $baseDir): SymbolLoaderInterface
    {
        return $this;
    }
}

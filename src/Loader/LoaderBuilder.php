<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Icanhazstring\Composer\Unused\Di\ServiceContainer;

class LoaderBuilder
{
    /** @var ServiceContainer */
    private $container;

    public function __construct(ServiceContainer $container)
    {
        $this->container = $container;
    }

    public function build(string $loaderClass, array $excludes = [], array $suggests = []): LoaderInterface
    {
        return $this->container->build($loaderClass, ['excludes' => $excludes, 'suggests' => $suggests]);
    }
}

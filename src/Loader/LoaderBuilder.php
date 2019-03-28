<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Zend\ServiceManager\ServiceManager;

class LoaderBuilder
{
    /** @var ServiceManager */
    private $container;

    public function __construct(ServiceManager $container)
    {
        $this->container = $container;
    }

    public function build(string $loaderClass, array $excludes = [], array $suggests = []): LoaderInterface
    {
        return $this->container->build($loaderClass, ['excludes' => $excludes, 'suggests' => $suggests]);
    }
}

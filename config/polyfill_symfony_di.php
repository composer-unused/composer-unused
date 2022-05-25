<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

assert(class_exists(ContainerConfigurator::class, true));

if (! function_exists('\Symfony\Component\DependencyInjection\Loader\Configurator\service')) {
    function service(string $serviceId): ReferenceConfigurator
    {
        return ref($serviceId);
    }
}

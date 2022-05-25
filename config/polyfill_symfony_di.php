<?php

declare(strict_types=1);

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

if (class_exists(ContainerConfigurator::class, true) && ! function_exists('\Symfony\Component\DependencyInjection\Loader\Configurator\service')) {
    function service(string $serviceId): ReferenceConfigurator
    {
        return ref($serviceId);
    }
}

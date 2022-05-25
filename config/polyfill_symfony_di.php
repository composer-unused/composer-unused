<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

if (! function_exists('Symfony\Component\DependencyInjection\Loader\Configurator\service')) {
    function service(string $serviceId): ReferenceConfigurator
    {
        return ref($serviceId);
    }
}

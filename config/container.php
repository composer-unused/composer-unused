<?php

declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

return (static function () {
    $container = new ContainerBuilder();
    $loader = new PhpFileLoader($container, new FileLocator(__DIR__));
    $loader->load('services.php');

    if (defined('ENV') && constant('ENV') === 'test') {
        $loader->load('services_test.php');
    }

    $container->compile();

    return $container;
})();

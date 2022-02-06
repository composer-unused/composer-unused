<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Test\Stubs\TestDetector;
use OndraM\CiDetector\CiDetectorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator) {
    $services = $configurator->services()
        ->defaults()
        ->autowire()
        ->public()
        ->autoconfigure();

    $services->set(CiDetectorInterface::class, TestDetector::class);
};

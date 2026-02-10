<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;

return static function (Configuration $config): Configuration {
    $config
        // ->addNamedFilter(NamedFilter::fromString('symfony/property-access'))
        // ->addPatternFilter(PatternFilter::fromString('/symfony\/.*/'))
        ->setAdditionalFilesFor('icanhazstring/composer-unused', [
            __FILE__,
            'bin/composer-unused',
            'config/container.php',
            'config/polyfill_symfony_di.php',
            'config/services.php',
            'config/services_test.php',
        ]);

    // symfony/serializer with php8.1 installs a version that is no longer suggesting property-access
    if (PHP_VERSION_ID >= 80100) {
        $config->addNamedFilter(NamedFilter::fromString('symfony/property-access'));
    }

    return $config;
};

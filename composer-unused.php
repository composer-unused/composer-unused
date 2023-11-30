<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\NamedFilter;
use ComposerUnused\ComposerUnused\Configuration\PatternFilter;
use Webmozart\Glob\Glob;

return static function (Configuration $config): Configuration {
    $config
        // ->addNamedFilter(NamedFilter::fromString('symfony/property-access'))
        // ->addPatternFilter(PatternFilter::fromString('/symfony\/.*/'))
        ->setAdditionalFilesFor('icanhazstring/composer-unused', [
            __FILE__,
            ...Glob::glob(__DIR__ . '/config/*.php'),
        ]);

    // symfony/serializer with php8.1 installs a version that is no longer suggesting property-access
    if (PHP_VERSION_ID >= 80100) {
        $config->addNamedFilter(NamedFilter::fromString('symfony/property-access'));
    }

    return $config;
};

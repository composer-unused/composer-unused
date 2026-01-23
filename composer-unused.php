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
            'bin/composer-unused',
            ...Glob::glob(__DIR__ . '/config/*.php'),
        ]);

    $config->addNamedFilter(NamedFilter::fromString('symfony/property-access'));

    return $config;
};

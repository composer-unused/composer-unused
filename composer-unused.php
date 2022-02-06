<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\PatternFilter;

return static function (Configuration $config): Configuration {
    return $config
        // ->addNamedFilter(NamedFilter::fromString('symfony/config'))
        // ->addPatternFilter(PatternFilter::fromString('/symfony-.*/'))
        ->setAdditionalFilesFor('icanhazstring/composer-unused', [
            __DIR__ . '/config/container.php'
        ]);
};

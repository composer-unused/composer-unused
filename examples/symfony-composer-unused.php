<?php

declare(strict_types=1);

use ComposerUnused\ComposerUnused\Configuration\Configuration;
use ComposerUnused\ComposerUnused\Configuration\ConfigurationSet\SymfonyConfigurationSet;

return static function (Configuration $config): Configuration {
    // Apply Symfony configuration set to scan additional directories
    $config->applyConfigurationSet(new SymfonyConfigurationSet('__root__'));

    return $config;
};
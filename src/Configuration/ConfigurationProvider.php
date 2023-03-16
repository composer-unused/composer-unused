<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Configuration;

/**
 * @internal
 */
final class ConfigurationProvider
{
    public function fromPath(string $configPath): Configuration
    {
        $configuration = new Configuration();

        if (!file_exists($configPath)) {
            return $configuration;
        }

        /** @var callable $configurationFactory */
        $configurationFactory = require $configPath;
        return $configurationFactory($configuration);
    }
}

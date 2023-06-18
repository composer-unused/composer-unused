<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\ComposerUnused\Composer\Exception\InvalidComposerVersionInstalledPackages;

class SupportedInstalledPackagesVersionChecker
{
    /**
     * @param array<string, array<string, string>> $decodedInstalledJson
     */
    public static function check(array $decodedInstalledJson): void
    {
        if (!array_key_exists('packages', $decodedInstalledJson)) {
            throw new InvalidComposerVersionInstalledPackages(
                "Packages are installed using Composer 1 which is deprecated."
            );
        }
    }
}

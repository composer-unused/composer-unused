<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Composer\Config;
use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

interface OutputFormatterInterface
{
    /**
     * @return int Exitcode
     */
    public function formatOutput(
        PackageInterface $rootPackage,
        string $composerJsonPath,
        DependencyCollection $usedDependencyCollection,
        DependencyCollection $unusedDependencyCollection,
        DependencyCollection $ignoredDependencyCollection,
        FilterCollection $filterCollection,
        OutputStyle $output
    ): int;
}

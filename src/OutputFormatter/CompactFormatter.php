<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * Only write one line with unused packages
 */
final class CompactFormatter implements OutputFormatterInterface
{
    public function formatOutput(
        PackageInterface $rootPackage,
        string $composerJsonPath,
        DependencyCollection $usedDependencyCollection,
        DependencyCollection $unusedDependencyCollection,
        DependencyCollection $ignoredDependencyCollection,
        FilterCollection $filterCollection,
        OutputStyle $output
    ): int {
        $unused = [];
        foreach ($unusedDependencyCollection as $dependency) {
            $unused[] = $dependency->getName();
        }
        if (count($unused) > 0) {
            $output->text(sprintf('Unused packages: %s', implode(', ', $unused)));
        }

        if ($unusedDependencyCollection->count() > 0 || count($filterCollection->getUnused()) > 0) {
            return 1;
        }

        return 0;
    }
}

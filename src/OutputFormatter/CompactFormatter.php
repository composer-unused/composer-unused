<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * Only write a line with unused packages and a line with zombie exclusions if applicable
 * The formatter thus only prints errors and in a compact manner
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

        $zombies = [];
        foreach ($filterCollection->getUnused() as $filter) {
            $zombies[] = $filter->toString();
        }
        if (count($zombies) > 0) {
            $output->text(sprintf('Zombie exclusions: %s', implode(' / ', $zombies)));
        }

        if ($unusedDependencyCollection->count() > 0 || count($filterCollection->getUnused()) > 0) {
            return 1;
        }

        return 0;
    }
}

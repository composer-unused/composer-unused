<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class DefaultFormatter implements OutputFormatterInterface
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
        $output->section('Results');

        $output->writeln(
            sprintf(
                'Found <fg=green>%d used</>, <fg=red>%d unused</>, <fg=yellow>%d ignored</> and <fg=magenta>%d zombie</> packages',
                count($usedDependencyCollection),
                count($unusedDependencyCollection),
                count($ignoredDependencyCollection),
                count($filterCollection->getUnused())
            )
        );

        $output->newLine();
        $output->text('<fg=green>Used packages</>');
        foreach ($usedDependencyCollection as $usedDependency) {
            $requiredBy = '';
            $suggestedBy = '';

            if (!empty($usedDependency->getRequiredBy())) {
                $requiredByNames = array_map(static function (DependencyInterface $dependency) {
                    return $dependency->getName();
                }, $usedDependency->getRequiredBy());

                $requiredBy = sprintf(
                    ' (<fg=cyan>required by: %s</>)',
                    implode(', ', $requiredByNames)
                );
            }

            if (!empty($usedDependency->getSuggestedBy())) {
                $suggestedByNames = array_map(static function (DependencyInterface $dependency) {
                    return $dependency->getName();
                }, $usedDependency->getSuggestedBy());

                $requiredBy = sprintf(
                    ' (<fg=cyan>suggested by: %s</>)',
                    implode(', ', $suggestedByNames)
                );
            }

            $name = $usedDependency->getName();
            $url = $usedDependency->getUrl();

            if ($url !== null) {
                $name = sprintf('%s (%s)', $name, $url);
            }

            $output->writeln(
                sprintf(
                    ' <fg=green>%s</> %s%s%s',
                    "\u{2713}",
                    $name,
                    $requiredBy,
                    $suggestedBy
                )
            );
        }

        $output->newLine();
        $output->text('<fg=red>Unused packages</>');
        foreach ($unusedDependencyCollection as $dependency) {
            $name = $dependency->getName();
            $url = $dependency->getUrl();

            if ($url !== null) {
                $name = sprintf('%s (%s)', $name, $url);
            }

            $output->writeln(
                sprintf(
                    ' <fg=red>%s</> %s',
                    "\u{2717}",
                    $name
                )
            );
        }

        $output->newLine();
        $output->text('<fg=yellow>Ignored packages</>');

        foreach ($ignoredDependencyCollection as $dependency) {
            $name = $dependency->getName();
            $url = $dependency->getUrl();

            if ($url !== null) {
                $name = sprintf('%s (%s)', $name, $url);
            }

            $output->writeln(
                sprintf(
                    ' <fg=yellow>%s</> %s (<fg=cyan>%s</>)',
                    "\u{25CB}",
                    $name,
                    $dependency->getStateReason()
                )
            );
        }

        $output->newLine();
        $output->text('<fg=magenta>Zombies exclusions</> (<fg=cyan>did not match any package</>)');

        foreach ($filterCollection->getUnused() as $filter) {
            $output->writeln(
                sprintf(
                    ' <fg=magenta>%s</> %s',
                    "\u{1F480}",
                    $filter->toString()
                )
            );
        }

        if ($unusedDependencyCollection->count() > 0 || count($filterCollection->getUnused()) > 0) {
            return 1;
        }

        return 0;
    }
}

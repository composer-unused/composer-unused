<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

/**
 * @see https://docs.gitlab.com/ee/ci/testing/code_quality.html#implement-a-custom-tool
 */
final class GitlabFormatter implements OutputFormatterInterface
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
        $jsonResult = [];

        foreach ($unusedDependencyCollection as $dependency) {
            $jsonResult[] = [
                'description' => str_replace("\n", '%0A', sprintf('%s is unused', $dependency->getName())),
                'fingerprint' => hash('sha256', (sprintf('unused:%s', $dependency->getName()))),
                'severity' => 'major',
                'location' => [
                    'path' => $composerJsonPath,
                    'lines' => [
                        'begin' => $rootPackage->getRequire($dependency->getName())->getLineNumber(),
                    ],
                ],
            ];
        }

        foreach ($ignoredDependencyCollection as $dependency) {
            $jsonResult[] = [
                'description' => str_replace("\n", '%0A', sprintf('%s was ignored', $dependency->getName())),
                'fingerprint' => hash('sha256', (sprintf('ignored:%s', $dependency->getName()))),
                'severity' => 'info',
                'location' => [
                    'path' => $composerJsonPath,
                    'lines' => [
                        'begin' => $rootPackage->getRequire($dependency->getName())->getLineNumber(),
                    ],
                ],
            ];
        }

        foreach ($filterCollection->getUnused() as $filter) {
            $jsonResult[] = [
                'description' => str_replace("\n", '%0A', sprintf('%s exclusion is a zombie', $filter->toString())),
                'fingerprint' => hash('sha256', (sprintf('zombie:%s', $filter->toString()))),
                'severity' => 'minor',
                'location' => [
                    'path' => $composerJsonPath,
                    'lines' => [
                        'begin' => 0,
                    ],
                ],
            ];
        }

        $json = json_encode($jsonResult, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $output->write($json);

        if ($unusedDependencyCollection->count() > 0 || count($filterCollection->getUnused()) > 0) {
            return 1;
        }

        return 0;
    }
}

<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class JsonFormatter implements OutputFormatterInterface
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

        foreach ($usedDependencyCollection as $usedDependency) {
            $jsonResult['used-packages'][] = $this->createUsedPackageJson($usedDependency);
        }

        foreach ($unusedDependencyCollection as $dependency) {
            $jsonResult['unused-packages'][] = $dependency->getName();
        }

        foreach ($ignoredDependencyCollection as $dependency) {
            $jsonResult['ignored-packages'][] = $dependency->getName();
        }

        foreach ($filterCollection->getUnused() as $filter) {
            $jsonResult['zombie-exclusions'][] = $filter->toString();
        }

        $json = json_encode($jsonResult, JSON_PRETTY_PRINT | JSON_THROW_ON_ERROR);
        $output->write($json);

        if ($unusedDependencyCollection->count() > 0 || count($filterCollection->getUnused()) > 0) {
            return 1;
        }

        return 0;
    }

    /**
     * @return array<string, mixed>
     */
    private function createUsedPackageJson(DependencyInterface $dependency): array
    {
        $packageJson = [
            'name' => $dependency->getName(),
        ];

        if (!empty($dependency->getRequiredBy())) {
            $requiredByNames = array_map(static function (DependencyInterface $dependency) {
                return $dependency->getName();
            }, $dependency->getRequiredBy());

            $packageJson['required-by'] = $requiredByNames;
        }

        if (!empty($dependency->getSuggestedBy())) {
            $suggestedByNames = array_map(static function (DependencyInterface $dependency) {
                return $dependency->getName();
            }, $dependency->getSuggestedBy());

            $packageJson['suggested-by'] = $suggestedByNames;
        }

        return $packageJson;
    }
}

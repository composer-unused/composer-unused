<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

class JUnitFormatter implements OutputFormatterInterface
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
        $xml = '<?xml version="1.0" encoding="UTF-8"?>';

        $totalFailuresCount = count($unusedDependencyCollection) + count($filterCollection->getUnused());
        $totalSkippedCount = count($ignoredDependencyCollection);
        $totalTestsCount = $totalFailuresCount + $totalSkippedCount;

        $xml .= sprintf(
            '<testsuite name="composer-unused" failures="%d" skipped="%d" tests="%d" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="https://raw.githubusercontent.com/junit-team/junit5/r5.5.1/platform-tests/src/test/resources/jenkins-junit.xsd">',
            $totalFailuresCount,
            $totalSkippedCount,
            $totalTestsCount
        );

        if (count($unusedDependencyCollection) > 0) {
            foreach ($unusedDependencyCollection as $dependency) {
                $xml .= sprintf('<testcase name="%s">', $this->escape($dependency->getName()));
                $xml .= '<failure type="ERROR" message="Dependency is unused" />';
                $xml .= '</testcase>';
            }
        }

        if (count($ignoredDependencyCollection) > 0) {
            foreach ($ignoredDependencyCollection as $dependency) {
                $xml .= sprintf('<testcase name="%s">', $this->escape($dependency->getName()));
                $xml .= '<skipped message="Dependency is ignored" />';
                $xml .= '</testcase>';
            }
        }

        if (count($filterCollection->getUnused()) > 0) {
            foreach ($filterCollection->getUnused() as $filter) {
                $xml .= sprintf('<testcase name="%s">', $this->escape($filter->toString()));
                $xml .= '<failure type="ERROR" message="Dependency did not match any package" />';
                $xml .= '</testcase>';
            }
        }

        $xml .= '</testsuite>';

        $output->write($xml);

        if ($unusedDependencyCollection->count() > 0 || count($filterCollection->getUnused()) > 0) {
            return 1;
        }

        return 0;
    }

    private function escape(string $string): string
    {
        return htmlspecialchars($string, ENT_XML1 | ENT_COMPAT, 'UTF-8');
    }
}

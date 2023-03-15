<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use ComposerUnused\ComposerUnused\Dependency\DependencyCollection;
use ComposerUnused\ComposerUnused\Filter\FilterCollection;
use ComposerUnused\Contracts\PackageInterface;
use Symfony\Component\Console\Style\OutputStyle;

final class GithubFormatter implements OutputFormatterInterface
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
        foreach ($unusedDependencyCollection as $dependency) {
            $metas = [
                'file' => $composerJsonPath,
                'line' => $rootPackage->getRequire($dependency->getName())->getLineNumber(),
                'col' => 0,
            ];

            array_walk($metas, static function (&$value, string $key): void {
                $value = sprintf('%s=%s', $key, (string)$value);
            });

            // newlines need to be encoded
            // see https://github.com/actions/starter-workflows/issues/68#issuecomment-581479448
            $message = str_replace("\n", '%0A', sprintf('%s is unused', $dependency->getName()));
            $line = sprintf('::error %s::%s', implode(',', $metas), $message);

            $output->write($line);
            $output->writeln('');
        }

        foreach ($ignoredDependencyCollection as $dependency) {
            $metas = [
                'file' => $composerJsonPath,
                'line' => $rootPackage->getRequire($dependency->getName())->getLineNumber(),
                'col' => 0,
            ];

            array_walk($metas, static function (&$value, string $key): void {
                $value = sprintf('%s=%s', $key, (string)$value);
            });

            // newlines need to be encoded
            // see https://github.com/actions/starter-workflows/issues/68#issuecomment-581479448
            $message = str_replace("\n", '%0A', sprintf('%s was ignored', $dependency->getName()));
            $line = sprintf('%s::%s', implode(',', $metas), $message);

            $output->write($line);
            $output->writeln('');
        }

        foreach ($filterCollection->getUnused() as $filter) {
            $metas = [
                'file' => $composerJsonPath,
                'line' => 0,
                'col' => 0,
            ];

            array_walk($metas, static function (&$value, string $key): void {
                $value = sprintf('%s=%s', $key, (string)$value);
            });

            // newlines need to be encoded
            // see https://github.com/actions/starter-workflows/issues/68#issuecomment-581479448
            $message = str_replace("\n", '%0A', sprintf('"%s" exclusion is a zombie', $filter->toString()));
            $line = sprintf('::warning %s::%s', implode(',', $metas), $message);

            $output->write($line);
            $output->writeln('');
        }

        if ($unusedDependencyCollection->count() > 0 || count($filterCollection->getUnused()) > 0) {
            return 1;
        }

        return 0;
    }
}

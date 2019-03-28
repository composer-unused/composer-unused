<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\PackageInterface;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

class PackageLoader implements LoaderInterface
{
    /** @var PackageSubjectFactory */
    private $subjectFactory;
    /** @var ResultInterface */
    private $loaderResult;
    /** @var RepositoryInterface */
    private $packageRepository;
    /** @var array */
    private $excludes;

    public function __construct(
        RepositoryInterface $packageRepository,
        PackageSubjectFactory $subjectFactory,
        ResultInterface $loaderResult,
        array $excludes = []
    ) {
        $this->subjectFactory = $subjectFactory;
        $this->loaderResult = $loaderResult;
        $this->packageRepository = $packageRepository;
        $this->excludes = $excludes;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     * @return ResultInterface
     */
    public function load(Composer $composer, SymfonyStyle $io): ResultInterface
    {
        $io->section('Loading packages');

        /** @var Link[] $requiredPackages */
        $requiredPackages = array_filter($composer->getPackage()->getRequires(), [$this, 'filterExcludes']);

        if (empty($requiredPackages)) {
            return $this->loaderResult;
        }

        $io->progressStart(\count($requiredPackages));

        foreach ($requiredPackages as $require) {
            $constraint = $require->getConstraint();

            if ($constraint === null) {
                $io->progressAdvance();
                $this->loaderResult->skipItem($require->getTarget(), 'Invalid constraint');
                continue;
            }

            $composerPackage = $this->packageRepository->findPackage($require->getTarget(), $constraint);

            if ($composerPackage === null) {
                $io->progressAdvance();
                $this->loaderResult->skipItem($require->getTarget(), 'Unable to locate package');
                continue;
            }

            if (!$this->packageHasValidNamespaces($composerPackage)) {
                $io->progressAdvance();
                $this->loaderResult->skipItem($require->getTarget(), 'Package provides no namespace');
                continue;
            }

            $this->loaderResult->addItem(($this->subjectFactory)($composerPackage));
            $io->progressAdvance();
        }

        $io->progressFinish();

        if (count($this->loaderResult->getItems()) !== count($requiredPackages)) {
            $io->note(sprintf('Found %d package(s) to be checked.', count($this->loaderResult->getItems())));
        }

        return $this->loaderResult;
    }

    public function filterExcludes(Link $package): bool
    {
        $exclude = in_array($package->getTarget(), $this->excludes, true);

        if ($exclude) {
            $this->loaderResult->skipItem($package->getTarget(), 'Package excluded by cli/config');
        }

        return !$exclude;
    }

    private function packageHasValidNamespaces(PackageInterface $package): bool
    {
        $autoload = array_merge_recursive(
            $package->getAutoload()['psr-0'] ?? [],
            $package->getAutoload()['psr-4'] ?? [],
            $package->getDevAutoload()['psr-0'] ?? [],
            $package->getDevAutoload()['psr-4'] ?? []
        );

        $namespaces = array_filter(array_keys($autoload), static function ($namespace) {
            return !empty($namespace);
        });

        return !empty($namespaces);
    }
}

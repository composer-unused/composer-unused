<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\FilterInterface;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;

class PackageLoader implements LoaderInterface
{
    /** @var PackageSubjectFactory */
    private $subjectFactory;
    /** @var ResultInterface */
    private $loaderResult;
    /** @var RepositoryInterface */
    private $packageRepository;
    /** @var PackageHelper */
    private $packageHelper;
    /** @var FilterInterface[] */
    private $packageFilters;

    public function __construct(
        RepositoryInterface $packageRepository,
        PackageSubjectFactory $subjectFactory,
        ResultInterface $loaderResult,
        PackageHelper $packageHelper,
        array $packageFilters
    ) {
        $this->subjectFactory = $subjectFactory;
        $this->loaderResult = $loaderResult;
        $this->packageRepository = $packageRepository;
        $this->packageHelper = $packageHelper;
        $this->packageFilters = $packageFilters;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     *
     * @return ResultInterface
     */
    public function load(Composer $composer, SymfonyStyle $io): ResultInterface
    {
        $io->section('Loading packages');

        /** @var Link[] $requiredPackages */
        $requiredPackages = $composer->getPackage()->getRequires();

        $io->progressStart(count($requiredPackages));

        foreach ($requiredPackages as $require) {
            $io->progressAdvance();

            if ($this->matchesPackageFilter($require)) {
                continue;
            }

            if ($this->packageHelper->isPhpExtension($require)) {
                $composerPackage = new Package(strtolower($require->getTarget()), '*', '*');
            } else {
                $composerPackage = $this->packageRepository->findPackage(
                    $require->getTarget(),
                    $require->getConstraint() ?? ''
                );
            }

            if ($composerPackage === null) {
                continue;
            }


            $this->loaderResult->addItem(($this->subjectFactory)($composerPackage));
        }

        $io->progressFinish();

        if (count($this->loaderResult->getItems()) !== count($requiredPackages)) {
            $io->note(sprintf('Found %d package(s) to be checked.', count($this->loaderResult->getItems())));
        }

        return $this->loaderResult;
    }

    private function matchesPackageFilter(Link $require): bool
    {
        foreach ($this->packageFilters as $packageFilter) {
            if ($packageFilter->match($require)) {
                $this->loaderResult->skipItem($require->getTarget(), $packageFilter->getReason());

                return true;
            }
        }

        return false;
    }
}

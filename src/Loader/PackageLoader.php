<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Package\Package;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\FilterInterface;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;

class PackageLoader implements LoaderInterface
{
    use ProgressBarTrait;

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

    /**
     * @param array<FilterInterface> $packageFilters
     */
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
        $this->io = $io;
        $io->section('Loading packages');

        $requiredPackages = $composer->getPackage()->getRequires();

        $this->progressStart(count($requiredPackages));

        foreach ($requiredPackages as $require) {
            $this->progressAdvance();

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

        $this->progressFinish();

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

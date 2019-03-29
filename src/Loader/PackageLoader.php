<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Composer\Package\Link;
use Composer\Repository\RepositoryInterface;
use Icanhazstring\Composer\Unused\Loader\Filter\FilterInterface;
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
    /** @var FilterInterface[] */
    private $packageFilters;

    public function __construct(
        RepositoryInterface $packageRepository,
        PackageSubjectFactory $subjectFactory,
        ResultInterface $loaderResult,
        array $packageFilters
    ) {
        $this->subjectFactory = $subjectFactory;
        $this->loaderResult = $loaderResult;
        $this->packageRepository = $packageRepository;
        $this->packageFilters = $packageFilters;
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
        $requiredPackages = $composer->getPackage()->getRequires();

        $io->progressStart(\count($requiredPackages));

        foreach ($requiredPackages as $require) {
            $io->progressAdvance();

            // Temporary solution to avoid ext- packages
            if (strpos($require->getTarget(), 'ext-') === 0) {
                continue 1;
            }

            foreach ($this->packageFilters as $packageFilter) {
                if ($packageFilter->match($require)) {
                    $this->loaderResult->skipItem($require->getTarget(), $packageFilter->getReason());
                    continue 2;
                }
            }

            $composerPackage = $this->packageRepository->findPackage(
                $require->getTarget(),
                $require->getConstraint() ?? ''
            );

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
}

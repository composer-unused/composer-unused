<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Subject\Factory\PackageSubjectFactory;
use Symfony\Component\Console\Style\SymfonyStyle;

class PackageLoader implements LoaderInterface
{
    /** @var PackageSubjectFactory */
    private $subjectFactory;

    public function __construct(PackageSubjectFactory $subjectFactory)
    {
        $this->subjectFactory = $subjectFactory;
    }

    /**
     * @param Composer     $composer
     * @param SymfonyStyle $io
     *
     * @return PackageInterface[]
     */
    public function load(Composer $composer, SymfonyStyle $io): array
    {
        $io->section('Loading packages');

        $requiredPackages = $composer->getPackage()->getRequires();
        $localRepo = $composer->getRepositoryManager()->getLocalRepository();

        $packages = [];
        /** @var string[] $skipped */
        $skipped = [];

        if (empty($requiredPackages)) {
            return [];
        }

        $io->text(sprintf('Loading %d requirements', count($requiredPackages)));
        $io->progressStart(\count($requiredPackages));

        foreach ($requiredPackages as $index => $require) {
            $constraint = $require->getConstraint();

            if ($constraint === null) {
                $io->progressAdvance();
                $skipped[] = $require->getTarget();
                continue;
            }

            $composerPackage = $localRepo->findPackage($require->getTarget(), $constraint);

            if ($composerPackage === null) {
                $io->progressAdvance();
                $skipped[] = $require->getTarget();
                continue;
            }

            $packages[] = ($this->subjectFactory)($composerPackage);
            $io->progressAdvance();
        }

        $io->progressFinish();

        if (count($skipped)) {
            $io->note(sprintf('Skipped %d requirements. No package found or invalid constraint.', count($skipped)));
            $io->listing($skipped);
        }

        return $packages;
    }
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Composer;
use Symfony\Component\Console\Style\SymfonyStyle;

interface LoaderInterface extends ProgressAwareInterface
{
    public function load(Composer $composer, SymfonyStyle $io): ResultInterface;
}

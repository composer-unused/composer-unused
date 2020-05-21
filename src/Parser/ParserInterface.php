<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Parser;

use Icanhazstring\Composer\Unused\Loader\ProgressAwareInterface;
use Icanhazstring\Composer\Unused\Loader\ResultInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

interface ParserInterface extends ProgressAwareInterface
{
    public function scan(string $baseDir, SymfonyStyle $io): ResultInterface;
}

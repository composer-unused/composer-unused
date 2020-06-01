<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

use Symfony\Component\Console\Style\SymfonyStyle;

trait ProgressBarTrait
{
    /** @var SymfonyStyle */
    protected $io;
    /** @var bool */
    protected $noProgress = false;

    protected function progressStart(int $count): void
    {
        if ($this->noProgress) {
            return;
        }

        $this->io->progressStart($count);
    }

    protected function progressAdvance(int $step = 1): void
    {
        if ($this->noProgress) {
            return;
        }

        $this->io->progressAdvance($step);
    }

    protected function progressFinish(): void
    {
        if ($this->noProgress) {
            return;
        }

        $this->io->progressFinish();
    }

    public function toggleProgress(bool $toggle): self
    {
        $this->noProgress = $toggle;
        return $this;
    }
}

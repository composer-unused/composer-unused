<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Progress;

use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class DefaultProgressBarDecorator implements ProgressBarInterface
{
    private ProgressBar $progressBar;

    private bool $noProgress;

    public function __construct(OutputInterface $io, int $max = 0, bool $noProgress = false)
    {
        $this->progressBar = new ProgressBar($io, $max);
        $this->noProgress = $noProgress;
    }

    public function start(): void
    {
        if ($this->isSetProgress()) {
            $this->progressBar->start();
        }
    }

    public function advance(): void
    {
        if ($this->isSetProgress()) {
            $this->progressBar->advance();
        }
    }

    public function finish(): void
    {
        if ($this->isSetProgress()) {
            $this->progressBar->finish();
            $this->progressBar->clear();
        }
    }

    private function isSetProgress(): bool
    {
        return !$this->noProgress;
    }
}

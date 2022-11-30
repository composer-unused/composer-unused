<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Progress;

interface ProgressBarInterface
{
    public function start(): void;

    public function advance(): void;

    public function finish(): void;
}

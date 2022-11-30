<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Progress;

class NullProgressBar implements ProgressBarInterface
{
    public function start(): void {}

    public function advance(): void {}

    public function finish(): void {}
}

<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Console\Progress;

interface ProgressBarInterface
{
    public function start(): void;

    public function setMessage(string $message): void;

    public function advance(): void;

    public function finish(): void;
}

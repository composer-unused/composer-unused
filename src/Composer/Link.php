<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\LinkInterface;

final class Link implements LinkInterface
{
    private string $target;
    private int $line;

    public function __construct(string $target, int $line)
    {
        $this->target = $target;
        $this->line = $line;
    }

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getLineNumber(): int
    {
        return $this->line;
    }
}

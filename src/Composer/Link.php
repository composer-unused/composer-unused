<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\LinkInterface;

final class Link implements LinkInterface
{
    private string $target;

    public function __construct(string $target)
    {
        $this->target = $target;
    }

    public function getTarget(): string
    {
        return $this->target;
    }
}

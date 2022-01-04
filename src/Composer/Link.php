<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Composer;

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

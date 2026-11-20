<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Test\Stubs;

use ComposerUnused\Contracts\LinkInterface;

final class TestLink implements LinkInterface
{
    public string $target;

    public function getTarget(): string
    {
        return $this->target;
    }

    public function getLineNumber(): int
    {
        return 0;
    }
}

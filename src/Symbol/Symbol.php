<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

final class Symbol
{
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function matches(Symbol $symbol): bool
    {
        return $this->name === $symbol->name;
    }
}

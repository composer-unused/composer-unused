<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

interface SymbolListInterface
{
    public function add(Symbol $symbol): self;

    public function addAll(array $symbols): self;

    public function contains(Symbol $symbol): bool;
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

final class Symbol implements SymbolInterface
{
    private $identifier;

    public function __construct(string $identifier)
    {
        $this->identifier = $identifier;
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }


    public function matches(SymbolInterface $symbol): bool
    {
        return $this->identifier === $symbol->getIdentifier();
    }
}

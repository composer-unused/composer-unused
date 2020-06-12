<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol;

final class NamespaceSymbol implements SymbolInterface
{
    /** @var string */
    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public static function fromClass(string $class): self
    {
        return new self(implode('\\', explode('\\', $class, -1)));
    }

    public function getIdentifier(): string
    {
        return $this->namespace;
    }

    public function matches(SymbolInterface $symbol): bool
    {
        return strpos($this->namespace, $symbol->getIdentifier()) === 0;
    }
}

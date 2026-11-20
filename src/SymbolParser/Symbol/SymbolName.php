<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Symbol;

final class SymbolName
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = ltrim($name, '\\');
    }

    public function isPartOf(SymbolName $other): bool
    {
        $currentParts = explode('\\', $this->name);
        $otherParts = explode('\\', $other->name);

        $lastCurrent = $currentParts[0];
        $firstOther = array_pop($otherParts);

        return $lastCurrent === $firstOther;
    }

    public function merge(SymbolName $other): SymbolName
    {
        $currentParts = explode('\\', $this->name, -1);
        $otherParts = explode('\\', $other->name, 1);

        if (empty($currentParts)) {
            return $other;
        }

        return new self(implode('\\', $currentParts) . '\\' .  $otherParts[0]);
    }

    public function getName(): string
    {
        return $this->name;
    }
}

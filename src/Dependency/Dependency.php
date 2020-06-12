<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Dependency;

use Icanhazstring\Composer\Unused\Symbol\Symbol;
use Icanhazstring\Composer\Unused\Symbol\SymbolListInterface;

final class Dependency implements DependencyInterface
{
    /** @var bool */
    private $hasOnlyFileAutoload = false;

    /** @var SymbolListInterface */
    private $classes;
    /** @var SymbolListInterface */
    private $constants;
    /** @var SymbolListInterface */
    private $functions;

    public function __construct(
        SymbolListInterface $classes,
        SymbolListInterface $functions,
        SymbolListInterface $constants
    ) {
        $this->classes = $classes;
        $this->functions = $functions;
        $this->constants = $constants;
    }

    public function withOnlyFileAutoload(): self
    {
        $clone = clone $this;
        $clone->hasOnlyFileAutoload = true;

        return $clone;
    }

    public function hasOnlyFileAutoload(): bool
    {
        return $this->hasOnlyFileAutoload;
    }

    public function provides(Symbol $symbol): bool
    {
        $symbolLists = [
            $this->classes,
            $this->functions,
            $this->constants
        ];

        if ($this->hasOnlyFileAutoload()) {
            $symbolLists = [
                $this->functions,
                $this->constants,
                $this->classes
            ];
        }

        foreach ($symbolLists as $symbolList) {
            if ($symbolList->contains($symbol)) {
                return true;
            }
        }

        return false;
    }
}

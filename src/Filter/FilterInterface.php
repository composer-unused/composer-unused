<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Filter;

use ComposerUnused\ComposerUnused\Dependency\DependencyInterface;

interface FilterInterface
{
    public function applies(DependencyInterface $dependency): bool;

    public function used(): bool;

    public function toString(): string;
}

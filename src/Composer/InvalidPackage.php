<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\Exception\LinkNotFoundException;
use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\PackageInterface;

final class InvalidPackage implements PackageInterface
{
    private string $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getAutoload(): array
    {
        return [];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): ?string
    {
        return null;
    }

    public function getRequires(): array
    {
        return [];
    }

    public function getSuggests(): array
    {
        return [];
    }

    public function getRequire(string $name): LinkInterface
    {
        throw LinkNotFoundException::fromMissingLink($name);
    }
}

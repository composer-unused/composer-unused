<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\Exception\LinkNotFoundException;
use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\PackageInterface;

final class Package implements PackageInterface
{
    private string $name;
    /** @var array<LinkInterface> */
    private array $requires = [];
    /** @var array<string> */
    private array $suggests = [];
    /** @phpstan-var array{psr-0?: array<string, string|string[]>, psr-4?: array<string, string|string[]>, classmap?: list<string>, files?: list<string>} */
    private array $autoload = [];

    /**
     * @param array<string, mixed> $autoload
     * @param array<LinkInterface> $requires
     * @param array<string> $suggests
     */
    public function __construct(string $name, array $autoload = [], array $requires = [], array $suggests = [])
    {
        $this->name = $name;
        $this->autoload = $autoload;
        $this->requires = $requires;
        $this->suggests = $suggests;
    }

    public function getAutoload(): array
    {
        return $this->autoload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRequires(): array
    {
        return $this->requires;
    }

    public function getSuggests(): array
    {
        return $this->suggests;
    }

    /**
     * @param array<LinkInterface> $requires
     */
    public function setRequires(array $requires): void
    {
        $this->requires = $requires;
    }

    /**
     * @param array<string> $suggests
     */
    public function setSuggests(array $suggests): void
    {
        $this->suggests = $suggests;
    }

    public function getRequire(string $name): LinkInterface
    {
        foreach ($this->requires as $require) {
            if ($require->getTarget() === $name) {
                return $require;
            }
        }

        throw LinkNotFoundException::fromMissingLink($name);
    }
}

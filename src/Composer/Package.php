<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\Exception\LinkNotFoundException;
use ComposerUnused\Contracts\LinkInterface;
use ComposerUnused\Contracts\PackageInterface;

final class Package implements PackageInterface
{
    private string $name;
    private ?string $url;
    /** @var array<string, LinkInterface> */
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
    public function __construct(
        string $name,
        ?string $url = null,
        array $autoload = [],
        array $requires = [],
        array $suggests = []
    ) {
        $this->name = $name;
        $this->url = $url;
        $this->autoload = $autoload;
        $this->suggests = $suggests;
        $this->requires = \array_combine(
            \array_map(static fn(LinkInterface $link): string => $link->getTarget(), $requires),
            $requires
        ) ?: [];
    }

    public function getAutoload(): array
    {
        return $this->autoload;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getRequires(): array
    {
        return array_values($this->requires);
    }

    public function getSuggests(): array
    {
        return $this->suggests;
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
        if (isset($this->requires[$name])) {
            return $this->requires[$name];
        }

        throw LinkNotFoundException::fromMissingLink($name);
    }
}

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Composer;

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

    public static function fromConfig(Config $config): self
    {
        $package = new self($config->getName());
        $package->autoload = $config->getAutoload();
        $package->requires = array_map(static function (string $name) {
            return new Link($name);
        }, array_keys($config->getRequire()));
        $package->suggests = array_keys($config->getSuggests());

        return $package;
    }

    public function __construct(string $name)
    {
        $this->name = $name;
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
}

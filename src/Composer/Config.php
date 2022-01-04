<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

final class Config
{
    /** @var array<string, mixed> */
    protected array $config = [];
    protected string $name;
    /** @var array<string, mixed> */
    private array $require = [];
    /** @var array<string, mixed> */
    private array $autoload = [];
    /** @var array<string, string>  */
    private array $suggests = [];

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array<string, mixed>
     */
    public function getRequire(): array
    {
        return $this->require;
    }

    /**
     * @return array<string, mixed>
     */
    public function getAutoload(): array
    {
        return $this->autoload;
    }

    /**
     * @return array<string, string>
     */
    public function getSuggests(): array
    {
        return $this->suggests;
    }

    public function get(string $property): string
    {
        $value = $this->config[$property] ?? null;

        if ($property === 'vendor-dir') {
            $value = $value ?? 'vendor';
        }

        return $value;
    }
}

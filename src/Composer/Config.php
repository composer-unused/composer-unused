<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Mapping\ClassMetadata;

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
    private array $suggest = [];
    /** @var array<string, mixed> */
    private array $extra = [];
    private string $rawContent;
    private string $baseDir;
    private ?string $url = null;

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
    public function getSuggest(): array
    {
        return $this->suggest;
    }

    /**
     * @return array<string, mixed>
     */
    public function getExtra(): array
    {
        return $this->extra;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function get(string $property): string
    {
        $value = $this->config[$property] ?? null;

        if ($property === 'vendor-dir') {
            $value = $value ?? 'vendor';
        }

        return $value;
    }

    public function setRaw(string $content): void
    {
        $this->rawContent = $content;
    }

    public function getRaw(): string
    {
        return $this->rawContent;
    }

    public function setBaseDir(string $basedir): void
    {
        $this->baseDir = $basedir;
    }

    public function getBaseDir(): string
    {
        return $this->baseDir;
    }

    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('name', new Assert\NotBlank(
            [],
            "Missing 'name' property in composer.json"
        ));
    }
}

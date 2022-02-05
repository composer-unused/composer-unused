<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\PackageInterface;
use ComposerUnused\Contracts\RepositoryInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

final class LocalRepository implements RepositoryInterface
{
    private string $vendorDir;
    private Serializer $serializer;

    public function __construct(string $vendorDir)
    {
        $this->vendorDir = $vendorDir;
        $encoders = [new JsonEncoder()];
        $normalizers = [new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function findPackage(string $name): ?PackageInterface
    {
        $packageDir = $this->vendorDir . DIRECTORY_SEPARATOR . $name;
        $packageComposerJson = $packageDir . DIRECTORY_SEPARATOR . 'composer.json';

        if (!file_exists($packageComposerJson)) {
            return null;
        }

        $jsonContent = file_get_contents($packageComposerJson);

        if ($jsonContent === false) {
            return null;
        }

        $config = $this->serializer->deserialize($jsonContent, Config::class, 'json');
        return PackageFactory::fromConfig($config, $jsonContent);
    }
}

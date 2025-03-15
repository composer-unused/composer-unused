<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use RuntimeException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;

final class ConfigFactory
{
    private SerializerInterface $serializer;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function fromPath(string $jsonPath): Config
    {
        $composerJson = file_get_contents($jsonPath);

        if ($composerJson === false) {
            throw new RuntimeException('Unable to read contents from ' . $jsonPath);
        }

        /** @var Config $config */
        $config = $this->serializer->deserialize($composerJson, Config::class, 'json');
        $config->setRaw($composerJson);
        $config->setBaseDir(dirname($jsonPath));
        $config->setFileName(basename($jsonPath));

        return $config;
    }
}

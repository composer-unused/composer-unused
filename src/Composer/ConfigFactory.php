<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Composer;

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

    public function fromComposerJson(string $jsonContent): Config
    {
        return $this->serializer->deserialize($jsonContent, Config::class, 'json');
    }
}

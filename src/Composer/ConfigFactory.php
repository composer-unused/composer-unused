<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\ComposerUnused\Composer\Validator\ValidatorFactory;
use InvalidArgumentException;
use RuntimeException;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class ConfigFactory
{
    private SerializerInterface $serializer;

    private ValidatorInterface $validator;

    public function __construct()
    {
        $encoders = [new JsonEncoder()];
        $normalizers = [new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];

        $this->serializer = new Serializer($normalizers, $encoders);

        $this->validator = ValidatorFactory::createValidator();
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

        $this->validate($config);

        return $config;
    }

    private function validate(Config $config): void
    {
        $violations = $this->validator->validate($config);

        if (count($violations) !== 0) {
            $message = 'Validation errors:';

            foreach ($violations as $violation) {
                $message .=  ' ' . $violation->getMessage();
            }

            throw new InvalidArgumentException($message);
        }
    }
}

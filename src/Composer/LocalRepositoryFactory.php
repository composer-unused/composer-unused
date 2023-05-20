<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\NameConverter\CamelCaseToSnakeCaseNameConverter;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;

final class LocalRepositoryFactory
{
    private PackageFactory $packageFactory;
    private ConfigFactory $configFactory;
    private Serializer $serializer;

    public function __construct(PackageFactory $packageFactory, ConfigFactory $configFactory)
    {
        $this->packageFactory = $packageFactory;
        $this->configFactory = $configFactory;

        $encoders = [new JsonEncoder()];
        $normalizers = [new PropertyNormalizer(null, new CamelCaseToSnakeCaseNameConverter())];

        $this->serializer = new Serializer($normalizers, $encoders);
    }

    public function create(Config $composerConfig): LocalRepository
    {
        $installedMetadata = new LocalPackageInstalledPath($composerConfig);

        /** @var array{root: array<mixed>, versions: array<string, array<mixed>>} $installedVersions */
        $installedVersions = array_merge_recursive(
            $this->parseSourceUrlsFromInstalledJson($installedMetadata->getInstalledJsonPath()),
            require $installedMetadata->getInstalledPhpArrayPath()
        );

        return new LocalRepository(
            new InstalledVersions($installedVersions),
            $this->packageFactory,
            $this->configFactory
        );
    }

    /**
     * @return array<mixed>
     */
    private function parseSourceUrlsFromInstalledJson(string $jsonPath): array
    {
        if (!file_exists($jsonPath) || !is_readable($jsonPath)) {
            return [];
        }

        /** @var string $json */
        $json = \file_get_contents($jsonPath);

        /** @var array<string, mixed> $installedJson */
        $installedJson = $this->serializer->decode($json, 'json');

        $sourceUrls = ['versions' => []];

        foreach ($installedJson['packages'] as $package) {

            if (!array_key_exists('source', $package)) {
                $sourceUrls['versions'][$package['name']] = ['url' => $package['dist']['url']];
                continue;
            }

            $packageUrl = $package['source']['url'];
            /** @var int $lastDotPosition */
            $lastDotPosition = strrpos($packageUrl, '.');
            /** @var string $replacedUrl */
            $replacedUrl = substr($packageUrl, 0, $lastDotPosition);

            $sourceUrls['versions'][$package['name']] = ['url' => $replacedUrl];
        }

        return $sourceUrls;
    }
}

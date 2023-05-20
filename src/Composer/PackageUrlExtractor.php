<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

class PackageUrlExtractor
{
    /**
     * @param array<mixed> $package
     * @return string
     */
    public function getUrl(array $package): string
    {
        if (!array_key_exists('source', $package)) {
            return $package['dist']['url'];
        }

        return $this->parseUrlFromGitSourceUrl($package['source']['url']);
    }

    private function parseUrlFromGitSourceUrl(string $packageUrl): string
    {
        /** @var int $lastDotPosition */
        $lastDotPosition = strrpos($packageUrl, '.');

        return substr($packageUrl, 0, $lastDotPosition);
    }
}

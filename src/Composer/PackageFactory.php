<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

use ComposerUnused\Contracts\PackageInterface;

final class PackageFactory
{
    public function fromConfig(Config $config): PackageInterface
    {
        $requireLines = $this->matchJsonLineWithLink(array_keys($config->getRequire()), $config->getRaw());

        return new Package(
            $config->getName(),
            $config->getUrl(),
            $config->getAutoload(),
            array_map(static function (string $name) use ($requireLines) {
                return new Link($name, $requireLines[$name]);
            }, array_keys($config->getRequire())),
            array_keys($config->getSuggest())
        );
    }

    /**
     * @param array<string> $requires
     * @return array<string, int>
     */
    private function matchJsonLineWithLink(array $requires, string $jsonContent): array
    {
        $jsonContent = stripslashes($jsonContent);

        $lines = explode(PHP_EOL, $jsonContent);
        $matches = [];

        foreach ($requires as $require) {
            foreach ($lines as $lineNumber => $line) {
                if (preg_match(sprintf('#%s#', $require), $line)) {
                    $matches[$require] = $lineNumber + 1;
                    break;
                }
            }
        }

        return $matches;
    }
}

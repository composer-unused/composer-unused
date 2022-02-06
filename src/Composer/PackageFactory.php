<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Composer;

final class PackageFactory
{
    public static function fromConfig(Config $config, string $jsonContent): Package
    {
        $requireLines = self::matchJsonLineWithLink(array_keys($config->getRequire()), $jsonContent);

        return new Package(
            $config->getName(),
            $config->getAutoload(),
            array_map(static function (string $name) use ($requireLines) {
                return new Link($name, $requireLines[$name]);
            }, array_keys($config->getRequire())),
            array_keys($config->getSuggests())
        );
    }

    /**
     * @param array<string> $requires
     * @return array<string, int>
     */
    private static function matchJsonLineWithLink(array $requires, string $jsonContent): array
    {
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

<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Symbol\Loader;

use Generator;
use Icanhazstring\Composer\Unused\Composer\PackageDecoratorInterface;
use Icanhazstring\Composer\Unused\Exception\IOException;
use Icanhazstring\Composer\Unused\Symbol\Provider\FileSymbolProvider;
use SplFileInfo;
use function sprintf;

class FileSymbolLoader implements SymbolLoaderInterface
{
    /** @var FileSymbolProvider */
    private $fileSymbolProvider;

    public function __construct(FileSymbolProvider $fileSymbolProvider)
    {
        $this->fileSymbolProvider = $fileSymbolProvider;
    }

    /**
     * @throws IOException
     */
    public function load(PackageDecoratorInterface $package): Generator
    {
        $files = [];

        foreach ($package->getAutoload()['files'] ?? [] as $path) {
            $files[] = new SplFileInfo(
                sprintf(
                    '%s/vendor/%s/%s',
                    $package->getBaseDir(),
                    $package->getName(),
                    $path
                )
            );
        }

        yield from $this->fileSymbolProvider->provide($package->getTargetDir(), $files);
    }
}

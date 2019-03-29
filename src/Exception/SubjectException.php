<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Exception;

use Composer\Package\PackageInterface;

class SubjectException extends \RuntimeException
{
    public static function provideNamespaces(PackageInterface $package): self
    {
        return new self(
            sprintf(
                'Exception caught checking namespaces for package %s with verion %s',
                $package->getName(),
                $package->getVersion()
            )
        );
    }
}

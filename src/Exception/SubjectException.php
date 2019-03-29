<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Exception;

use Composer\Package\PackageInterface;
use Throwable;

class SubjectException extends \RuntimeException
{
    public static function provideNamespaces(PackageInterface $package, Throwable $previous): self
    {
        return new self(
            sprintf(
                'Exception caught checking namespaces for package %s with verion %s',
                $package->getName(),
                $package->getVersion()
            ),
            0,
            $previous
        );
    }
}

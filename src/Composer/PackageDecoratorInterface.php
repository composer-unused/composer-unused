<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Composer;

use Composer\Package\PackageInterface;

interface PackageDecoratorInterface extends PackageInterface
{
    public function getBaseDir(): string;
}

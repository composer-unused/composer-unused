<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject\Factory;

use Composer\Package\Package;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;

class PackageSubjectFactory
{
    public function __invoke(Package $composerPackage)
    {
        return new PackageSubject($composerPackage);
    }
}

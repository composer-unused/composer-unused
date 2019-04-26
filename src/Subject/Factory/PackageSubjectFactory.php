<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject\Factory;

use Composer\Package\PackageInterface;
use Icanhazstring\Composer\Unused\Subject\PackageSubject;

class PackageSubjectFactory
{
    public function __invoke(PackageInterface $composerPackage): PackageSubject
    {
        return new PackageSubject($composerPackage);
    }
}

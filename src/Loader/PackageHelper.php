<?php

namespace Icanhazstring\Composer\Unused\Loader;

use Composer\Package\Link;

final class PackageHelper
{
    public function isPhpExtension(Link $require): bool
    {
        return strpos($require->getTarget(), 'ext-') === 0 || $require->getTarget() === 'php';
    }
}

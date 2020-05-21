<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Loader;

interface ProgressAwareInterface
{
    /**
     * @return static
     */
    public function toggleProgress(bool $flag);
}

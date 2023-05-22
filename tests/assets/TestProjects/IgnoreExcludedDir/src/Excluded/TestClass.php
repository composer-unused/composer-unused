<?php

declare(strict_types=1);

namespace IgnoreExcludedDir\Excluded;

use dependency\src\DependencyClass;

class TestClass
{
    public function testMethod(): void
    {
        $a = new DependencyClass();
    }
}

<?php

declare(strict_types=1);

namespace IgnoreExcludedDir\Excluded;

use FileDependency\DependencyClass;

class TestClass
{
    public function testMethod(): void
    {
        $a = new DependencyClass();
    }
}

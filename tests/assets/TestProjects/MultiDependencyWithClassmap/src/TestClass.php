<?php

declare(strict_types=1);

namespace assets\TestProjects\MultiDependencyWithClassmap\src;

use First\Dependency\FirstDependency;
use Second\Dependency\SecondDependency;

final class TestClass
{
    public function test(): void
    {
        $first = new FirstDependency();
        $second = new SecondDependency();
    }
}

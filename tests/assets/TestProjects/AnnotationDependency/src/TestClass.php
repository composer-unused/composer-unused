<?php

declare(strict_types=1);

namespace AnnotationDependency;

class TestClass
{
    /** @First\Dependency\FirstDependency */
    public function testMethod(): void
    {
        $a = strlen('test');
    }
}

<?php

declare(strict_types=1);

namespace ExtMemcachedRequirement;

use Memcached;

class TestClass
{
    public function testMethod(Memcached $memcached): void
    {
        $a = Memcached::RES_SUCCESS;
    }
}

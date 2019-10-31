<?php

declare(strict_types=1);

namespace TestFile {

    class ClassWithZendOpcache
    {
        public function foo() {
            opcache_reset();
        }
    }

}

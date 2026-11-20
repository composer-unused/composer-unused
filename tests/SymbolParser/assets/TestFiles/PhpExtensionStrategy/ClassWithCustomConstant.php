<?php

declare(strict_types=1);

namespace TestFile {

    class ClassWithCustomInterface
    {
        private const JSON_PRETTY_PRINT = 1;

        public function foo() {
            return self::JSON_PRETTY_PRINT;
        }
    }

}

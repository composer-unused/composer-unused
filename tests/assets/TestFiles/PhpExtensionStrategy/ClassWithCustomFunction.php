<?php

declare(strict_types=1);

namespace TestFile {

    function json_encode() {

    };

    class ClassWithExtensionFunction
    {
        public function foo() {
            return TestFile\json_encode();
        }
    }

}

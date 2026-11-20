<?php

declare(strict_types=1);

namespace TestFile {

    class ClassWithExtensionFunction
    {
        public function foo() {
            return json_encode([
                'foo' => 'bar'
            ]);
        }
    }

}

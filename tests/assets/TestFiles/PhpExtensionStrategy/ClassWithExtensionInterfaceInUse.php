<?php

declare(strict_types=1);

namespace TestFile\WithUse {

    use \JsonSerializable;

    class ClassWithExtensionInterface implements JsonSerializable
    {
        public function jsonSerialize()
        {
            // TODO: Implement jsonSerialize() method.
        }
    }

}

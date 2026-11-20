<?php

declare(strict_types=1);

namespace TestFile {
use JsonSerializable;

    class ClassWithExtensionInterface implements JsonSerializable
    {
        public function jsonSerialize()
        {
        }
    }

}

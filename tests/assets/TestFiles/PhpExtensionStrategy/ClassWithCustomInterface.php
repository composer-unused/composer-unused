<?php

declare(strict_types=1);

namespace TestFile\WithUse\WithSameName {

    interface JsonSerializable
    {

    }

    class ClassWithExtensionInterface implements JsonSerializable, \TestFile\CustomJson\JsonSerializable
    {
        public function jsonSerialize()
        {
            // TODO: Implement jsonSerialize() method.
        }
    }

}
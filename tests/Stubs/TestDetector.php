<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Stubs;

use OndraM\CiDetector\Ci\CiInterface;
use OndraM\CiDetector\CiDetectorInterface;
use OndraM\CiDetector\Exception\CiNotDetectedException;

final class TestDetector implements CiDetectorInterface
{
    public function isCiDetected(): bool
    {
        return false;
    }

    public function detect(): CiInterface
    {
        throw new CiNotDetectedException();
    }
}

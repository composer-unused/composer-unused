<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\Test\Stubs;

use OndraM\CiDetector\Ci\CiInterface;
use OndraM\CiDetector\CiDetectorInterface;
use OndraM\CiDetector\Env;
use OndraM\CiDetector\Exception\CiNotDetectedException;

final class TestDetector implements CiDetectorInterface
{
    private ?CiInterface $ci = null;

    /**
     * @template T of CiInterface
     * @param class-string<T>|null $ciClass
     */
    public function __construct(?string $ciClass = null)
    {
        if ($ciClass !== null) {
            $this->ci = new $ciClass(new Env());
        }
    }

    public function isCiDetected(): bool
    {
        return false;
    }

    public function detect(): CiInterface
    {
        if ($this->ci === null) {
            throw new CiNotDetectedException();
        }

        return $this->ci;
    }
}

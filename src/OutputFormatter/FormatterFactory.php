<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\CiDetectorInterface;
use OndraM\CiDetector\Exception\CiNotDetectedException;

final class FormatterFactory
{
    private CiDetectorInterface $ciDetector;

    public function __construct(CiDetectorInterface $ciDetector)
    {
        $this->ciDetector = $ciDetector;
    }

    public function create(?string $type): OutputFormatterInterface
    {
        if ($type === null) {
            try {
                $ci = $this->ciDetector->detect();
                if ($ci->getCiName() === CiDetector::CI_GITHUB_ACTIONS) {
                    $type = 'github';
                }
            } catch (CiNotDetectedException $exception) {
                $type = 'default';
            }
        }

        switch ($type) {
            case 'github':
                return new GithubFormatter();
            case 'json':
                return new JsonFormatter();
            default:
                return new DefaultFormatter();
        }
    }
}

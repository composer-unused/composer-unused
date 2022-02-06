<?php

declare(strict_types=1);

namespace ComposerUnused\ComposerUnused\OutputFormatter;

use OndraM\CiDetector\CiDetector;
use OndraM\CiDetector\Exception\CiNotDetectedException;

final class FormatterFactory
{
    public static function create(?string $type): OutputFormatterInterface
    {
        $ciDetector = new CiDetector();

        try {
            $ci = $ciDetector->detect();
            if ($ci->getCiName() === CiDetector::CI_GITHUB_ACTIONS) {
                $type = 'github';
            }
        } catch (CiNotDetectedException $exception) {
            $type = 'default';
        }

        switch ($type) {
            case 'github':
                return new GithubFormatter();
            default:
                return new DefaultFormatter();
        }
    }
}

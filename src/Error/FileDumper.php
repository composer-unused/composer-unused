<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Error;

use Composer\Composer;
use Composer\Json\JsonFile;
use Icanhazstring\Composer\Unused\Error\Handler\ErrorHandlerInterface;

class FileDumper implements ErrorDumperInterface
{
    /** @var string */
    private $path;
    /** @var Composer */
    private $composer;

    public function __construct(string $path, Composer $composer)
    {
        $this->path = $path;
        $this->composer = $composer;
    }

    /**
     * @param ErrorHandlerInterface $errorHandler
     * @return string|null
     */
    public function dump(ErrorHandlerInterface $errorHandler): ?string
    {
        $jsonFile = new JsonFile($this->path);

        try {
            $file = [
                'version'      => $this->composer->getPackage()->getPrettyVersion(),
                'requires'     => [],
                'dev-requires' => [],
                'autoload'     => $this->composer->getPackage()->getAutoload(),
                'dev-autoload' => $this->composer->getPackage()->getDevAutoload(),
                'errors'       => []
            ];

            foreach ($this->composer->getPackage()->getRequires() as $name => $require) {
                $file['requires'][$name] = $require->getPrettyConstraint();
            }

            foreach ($this->composer->getPackage()->getDevRequires() as $name => $require) {
                $file['dev-requires'][$name] = $require->getPrettyConstraint();
            }

            foreach ($errorHandler->getErrors() as $error) {
                $file['errors'][] = [
                    'message' => $error->getMessage(),
                    'file'    => $error->getFile(),
                    'line'    => $error->getLine()
                ];
            }

            $jsonFile->write($file);
        } catch (\Exception $e) {
            return null;
        }

        return $jsonFile->getPath();
    }
}

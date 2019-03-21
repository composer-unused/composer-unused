<?php

declare(strict_types=1);

namespace Icanhazstring\Composer\Unused\Subject;

use PhpParser\Node;
use SplFileInfo;

class NamespaceUsage implements UsageInterface
{
    /** @var string */
    private $namespace;
    /** @var Node */
    private $node;
    /** @var SplFileInfo */
    private $file;

    public function __construct(SplFileInfo $file, string $namespace, Node $node)
    {
        $this->file = $file;
        $this->namespace = $namespace;
        $this->node = $node;
    }

    public function getFile(): SplFileInfo
    {
        return $this->file;
    }

    public function getNamespace(): string
    {
        return $this->namespace;
    }

    public function getLine(): int
    {
        return $this->node->getLine();
    }
}

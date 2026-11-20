<?php

declare(strict_types=1);

namespace ComposerUnused\SymbolParser\Parser\PHP;

use PhpParser\Node\Name;
use PhpParser\Node\Stmt;

use function strtolower;

/**
 * @author Laurent Laville
 */
class NameContext extends \PhpParser\NameContext
{
    public function getResolvedName(Name $name, int $type): ?Name
    {
        if ($type === Stmt\Use_::TYPE_NORMAL && $name->isSpecialClassName()) {
            return $this->aliases[$type][strtolower($name->name)] ?? null;
        }
        return parent::getResolvedName($name, $type);
    }
}

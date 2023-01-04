<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\ValueObjects\Tree;

/**
 * @method Tree[] getIterator()
 * @method Tree offsetGet(int|string $offset)
 * @method Tree last()
 */
class TreeCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Tree::class;
}

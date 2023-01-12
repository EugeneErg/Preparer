<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\ValueObjects\Branch;

/**
 * @method Branch[] getIterator()
 * @method Branch offsetGet(int|string $offset)
 * @method Branch|null last()
 */
class BranchCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Branch::class;
}

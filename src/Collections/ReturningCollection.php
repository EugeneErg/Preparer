<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\Returning;

/**
 * @method Returning[] getIterator()
 * @method Returning offsetGet(int|string $offset)
 */
class ReturningCollection extends AbstractCollection
{
    protected const ITEM_TYPE = Returning::class;
}

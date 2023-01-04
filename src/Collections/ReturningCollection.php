<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\Returning;

/**
 * @method Returning[] getIterator()
 * @method Returning offsetGet(int|string $offset)
 */
class ReturningCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Returning::class;
}

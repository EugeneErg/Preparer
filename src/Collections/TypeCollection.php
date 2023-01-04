<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\Types\FieldTypeInterface;

/**
 * @method FieldTypeInterface offsetGet(int|string $offset)
 * @method FieldTypeInterface[] getIterator()
 */
class TypeCollection extends ObjectCollection
{
    protected const ITEM_TYPE = FieldTypeInterface::class;
}

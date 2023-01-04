<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\Types\FieldTypeInterface;

/**
 * @method FieldTypeInterface[] getIterator()
 */
final class FieldCollection extends ObjectCollection
{
    protected const ITEM_TYPE = FieldTypeInterface::class;
}

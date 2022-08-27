<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\Types\FieldTypeInterface;

class TypeCollection extends AbstractCollection
{
    protected const ITEM_TYPE = FieldTypeInterface::class;
}

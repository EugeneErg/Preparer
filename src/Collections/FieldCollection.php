<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\Types\FieldTypeInterface;

final class FieldCollection extends AbstractImmutableCollection
{
    protected const ITEM_TYPE = FieldTypeInterface::class;
}

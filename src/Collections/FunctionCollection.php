<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\Functions\AbstractFunction;

class FunctionCollection extends AbstractCollection
{
    protected const ITEM_TYPE = AbstractFunction::class;
}

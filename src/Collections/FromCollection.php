<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\Functions\Query\From;

class FromCollection extends AbstractImmutableCollection
{
    protected const ITEM_TYPE = From::class;
}

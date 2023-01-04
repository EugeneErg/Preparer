<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\Functions\Query\From;


class FromCollection extends ObjectCollection
{
    protected const ITEM_TYPE = From::class;
}

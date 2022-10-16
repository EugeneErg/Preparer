<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\Returning;

class MutableReturningCollection extends AbstractCollection
{
    protected const ITEM_TYPE = Returning::class;
}

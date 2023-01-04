<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\Returning;

class MutableReturningCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Returning::class;
}

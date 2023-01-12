<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\DataTransferObjects\Destination;

class DestinationCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Destination::class;
}

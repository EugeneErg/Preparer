<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\DataTransferObjects\Connection;

/**
 * @method Connection[] getIterator()
 */
final class ConnectionCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Connection::class;
}

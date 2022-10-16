<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\DataTransferObjects\Connection;

final class ConnectionCollection extends AbstractImmutableCollection
{
    protected const ITEM_TYPE = Connection::class;
}

<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\Types\QueryTypeInterface;

class QueryTypeCollection extends ObjectCollection implements QueryTypeCollectionInterface
{
    protected const ITEM_TYPE = QueryTypeInterface::class;
}

<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\CollectionCollection;

final class Matrix extends CollectionCollection
{
    protected const ITEM_TYPE = ConnectionCollection::class;
}

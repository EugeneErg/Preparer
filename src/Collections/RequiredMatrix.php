<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\CollectionCollection;

class RequiredMatrix extends CollectionCollection
{
    protected const ITEM_TYPE = RequiredCollection::class;
}

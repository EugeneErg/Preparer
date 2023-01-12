<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\ValueObjects\Required;

/**
 * @method Required offsetGet(int|string $offset)
 */
class RequiredCollection extends ObjectCollection
{
    protected const ITEM_TYPE = Required::class;
}

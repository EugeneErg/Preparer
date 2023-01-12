<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Collections\ObjectCollection;
use EugeneErg\Preparer\Functions\AbstractFunction;

/**
 * @method AbstractFunction[] getIterator()
 * @method self slice(int $offset = 0, int|null $length = null, bool $preserveKeys = false)
 * @method self reverse(bool $preserveKeys = false)
 * @method AbstractFunction|null first()
 * @method AbstractFunction|null last()
 */
class FunctionCollection extends ObjectCollection
{
    protected const ITEM_TYPE = AbstractFunction::class;
}

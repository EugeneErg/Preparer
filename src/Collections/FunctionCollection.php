<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

use EugeneErg\Preparer\Functions\AbstractFunction;

/**
 * @method AbstractFunction[] getIterator()
 * @method AbstractFunction[] slice(int $offset = 0, int|null $length = null, bool $preserveKeys = false)
 * @method FunctionCollection reverse(bool $preserveKeys = false)
 */
class FunctionCollection extends AbstractImmutableCollection
{
    protected const ITEM_TYPE = AbstractFunction::class;
}

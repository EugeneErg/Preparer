<?php declare(strict_types=1);

namespace EugeneErg\Preparer\Collections;

final class Matrix extends AbstractImmutableCollection
{
    protected const ITEM_TYPE = ConnectionCollection::class;

    public function offsetGet($offset): mixed
    {

        return parent::offsetGet($offset);
    }
}

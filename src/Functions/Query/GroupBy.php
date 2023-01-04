<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Collections\TypeCollection;
use EugeneErg\Preparer\Types\FieldTypeInterface;
use EugeneErg\Preparer\Types\TypeInterface;

class GroupBy extends AbstractQueryFunction
{
    public readonly TypeCollection $values;

    public function __construct(FieldTypeInterface ...$values)
    {
        $this->values = new TypeCollection($values);
    }
}

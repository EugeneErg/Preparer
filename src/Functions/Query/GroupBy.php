<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Types\FieldTypeInterface;

class GroupBy extends AbstractQueryFunction
{
    public function __construct(public readonly FieldTypeInterface $value)
    {
    }
}

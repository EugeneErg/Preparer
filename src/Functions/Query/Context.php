<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Types\QueryTypeInterface;

class Context extends AbstractQueryFunction
{
    public function __construct(public readonly QueryTypeInterface $value)
    {
    }
}

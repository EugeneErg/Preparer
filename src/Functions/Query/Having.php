<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Query;

use EugeneErg\Preparer\Types\BooleanType;

class Having extends AbstractQueryFunction
{
    public function __construct(public readonly BooleanType $value)
    {
    }
}
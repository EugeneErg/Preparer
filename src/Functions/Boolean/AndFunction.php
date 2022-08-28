<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Boolean;

use EugeneErg\Preparer\Types\BooleanType;

class AndFunction extends AbstractBooleanFunction
{
    public function __construct(public readonly BooleanType $value)
    {
    }
}

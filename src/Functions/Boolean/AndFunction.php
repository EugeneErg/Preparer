<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Boolean;

use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\TypeInterface;

class AndFunction extends AbstractBooleanFunction
{
    public function __construct(TypeInterface $context, public readonly BooleanType $value)
    {
        parent::__construct($context);
    }
}

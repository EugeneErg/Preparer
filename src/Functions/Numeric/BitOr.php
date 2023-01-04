<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\TypeInterface;

class BitOr extends AbstractNumericFunction
{
    public function __construct(public readonly NumericType $value)
    {
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $function->value === $this->value;
    }
}

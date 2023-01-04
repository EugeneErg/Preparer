<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\TypeInterface;

class Log extends AbstractNumericFunction
{
    public function __construct(public readonly NumericType $base)
    {
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $function->base === $this->base;
    }
}

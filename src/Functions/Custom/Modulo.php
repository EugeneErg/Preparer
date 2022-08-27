<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Custom;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\NumericType;

class Modulo extends AbstractFunction
{
    public function __construct(public readonly NumericType $value)
    {
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $this->value === $function->value;
    }
}

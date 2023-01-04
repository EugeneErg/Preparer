<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\TypeInterface;

class BitAnd extends AbstractNumericFunction
{
    public function __construct(TypeInterface $context, public readonly NumericType $value)
    {
        parent::__construct($context);
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $function->value === $this->value;
    }
}

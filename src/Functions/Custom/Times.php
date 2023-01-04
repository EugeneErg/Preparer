<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Custom;

use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\TypeInterface;

class Times extends AbstractFunction
{
    public function __construct(TypeInterface $context, public readonly NumericType $value)
    {
        parent::__construct($context);
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $this->value === $function->value;
    }
}

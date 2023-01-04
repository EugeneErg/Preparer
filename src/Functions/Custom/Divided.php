<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Custom;

use EugeneErg\Preparer\Enums\RoundTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\TypeInterface;

class Divided extends AbstractFunction
{
    public function __construct(
        public readonly NumericType $value,
        public readonly ?RoundTypeEnum $roundType,
    ) {
    }

    public function equals(AbstractFunction $function): bool
    {
        return parent::equals($function)
            && $function->value === $this->value
            && $function->roundType === $this->roundType;
    }
}

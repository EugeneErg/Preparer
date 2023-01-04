<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Types\TypeInterface;

class ToNumeric extends AbstractNumericFunction
{
    public function __construct(
        TypeInterface $context,
        public readonly NumericTypeEnum $numberType = NumericTypeEnum::Float,
        public readonly ?int $digitsCount = null,
        public readonly ?int $accuracy = null,
    ) {
        parent::__construct($context);
    }
}

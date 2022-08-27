<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Enums\NumericTypeEnum;

class ToNumeric extends AbstractNumericFunction
{
    public function __construct(
        public readonly NumericTypeEnum $numberType = NumericTypeEnum::Float,
        public readonly ?int $digitsCount = null,
        public readonly ?int $accuracy = null,
    ) {
    }
}

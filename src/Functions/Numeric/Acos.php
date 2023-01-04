<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Types\TypeInterface;

class Acos extends AbstractNumericFunction
{
    public function __construct(
        TypeInterface $context,
        public readonly AngleTypeEnum $angleType = AngleTypeEnum::Degrees,
    ) {
        parent::__construct($context);
    }
}

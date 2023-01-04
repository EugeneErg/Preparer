<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Types\TypeInterface;

class Atan extends AbstractNumericFunction
{
    public function __construct(public readonly AngleTypeEnum $angleType = AngleTypeEnum::Degrees)
    {
    }
}

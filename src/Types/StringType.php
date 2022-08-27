<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Functions\Angle\ToAngle;
use EugeneErg\Preparer\Functions\Boolean\ToBoolean;
use EugeneErg\Preparer\Functions\Numeric\ToNumeric;

class StringType extends AbstractFieldType
{
    public function toAngle(
        AngleTypeEnum $type,
        NumericTypeEnum $numericType = NumericTypeEnum::Float,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): AngleType {
        return $this->call(new ToAngle($type, $numericType, $digitsCount, $accuracy));
    }

    public function toNumeric(
        NumericTypeEnum $type = NumericTypeEnum::Float,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): NumericType {
        return $this->call(new ToNumeric($type, $digitsCount, $accuracy));
    }

    public function toBoolean(): BooleanType
    {
        return $this->call(new ToBoolean());
    }
}

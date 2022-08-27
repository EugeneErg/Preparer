<?php

declare(strict_types=1);

namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Data\Value;
use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Types\AngleType;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\StringType;
use JetBrains\PhpStorm\Pure;

class Create
{
    public static function numeric(
        float $value,
        NumericTypeEnum $type = NumericTypeEnum::Float,
        int $digitsCount = null,
        int $accuracy = null,
    ): NumericType {
        return (new Value(['value' => $value]))->getNumeric('value', $type, $digitsCount, $accuracy);
    }

    public static function string(string $value): StringType
    {
        return (new Value(['value' => $value]))->getString('value');
    }

    public static function boolean(bool $value): BooleanType
    {
        return (new Value(['value' => $value]))->getBoolean('value');
    }

    public static function angle(
        float $value,
        AngleTypeEnum $type = AngleTypeEnum::Degrees,
        NumericTypeEnum $numericType = NumericTypeEnum::Float,
    ): AngleType {
        return (new Value(['value' => $value]))->getAngle('value', $type, $numericType);
    }

    #[Pure] public static function object(array $value): Value
    {
        return new Value($value);
    }
}

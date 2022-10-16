<?php

declare(strict_types=1);

namespace EugeneErg\Preparer;

use EugeneErg\Preparer\Data\Value;
use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Types\AngleType;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\StringType;

final class Create
{
    public static function numeric(
        float $value
    ): NumericType {
        return (new Value(['value' => $value]))->getNumeric('value');
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
    ): AngleType {
        return (new Value(['value' => $value]))->getAngle('value', $type);
    }

    public static function object(array $value): Value
    {
        return new Value($value);
    }
}

<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Functions\Numeric;

use EugeneErg\Preparer\Enums\TypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Types\AngleType;
use EugeneErg\Preparer\Types\BooleanType;
use EugeneErg\Preparer\Types\NumericType;
use EugeneErg\Preparer\Types\ObjectType;
use EugeneErg\Preparer\Types\StringType;
use EugeneErg\Preparer\Types\TypeInterface;

class GetField extends AbstractFunction
{
    public function __construct(public readonly TypeEnum $type, public readonly string $field)
    {
    }

    protected function getType(TypeInterface $type): string
    {
        return match ($this->type) {
            TypeEnum::Float,
            TypeEnum::Decimal,
            TypeEnum::Integer => NumericType::class,
            TypeEnum::DecimalRadians,
            TypeEnum::DecimalDegrees,
            TypeEnum::FloatRadians,
            TypeEnum::FloatDegrees,
            TypeEnum::IntegerRadians,
            TypeEnum::IntegerDegrees => AngleType::class,
            TypeEnum::Boolean => BooleanType::class,
            TypeEnum::Object => ObjectType::class,
            TypeEnum::String => StringType::class,
        };
    }
}

<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Enums\TypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Numeric\GetField;

abstract class AbstractDataType extends AbstractType implements CountableTypeInterface
{
    /**
     * @return NumericType|AngleType|StringType|BooleanType|ObjectType
     */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }

    public function getNumeric(string $field, NumericTypeEnum $type = NumericTypeEnum::Float): NumericType
    {
        return $this->call(new GetField(match ($type) {
            NumericTypeEnum::Float => TypeEnum::Float,
            NumericTypeEnum::Decimal => TypeEnum::Decimal,
            NumericTypeEnum::Integer => TypeEnum::Integer,
        }, $field));
    }

    public function getAngle(
        string $field,
        AngleTypeEnum $type = AngleTypeEnum::Degrees,
        NumericTypeEnum $numericType = NumericTypeEnum::Float,
    ): AngleType {
        return $this->call(new GetField(match ($numericType) {
            NumericTypeEnum::Float => match ($type) {
                AngleTypeEnum::Degrees => TypeEnum::FloatDegrees,
                AngleTypeEnum::Radians => TypeEnum::FloatRadians,
            },
            NumericTypeEnum::Decimal => match ($type) {
                AngleTypeEnum::Degrees => TypeEnum::DecimalDegrees,
                AngleTypeEnum::Radians => TypeEnum::DecimalRadians,
            },
            NumericTypeEnum::Integer => match ($type) {
                AngleTypeEnum::Degrees => TypeEnum::IntegerDegrees,
                AngleTypeEnum::Radians => TypeEnum::IntegerRadians,
            },
        }, $field));
    }

    public function getString(string $field): StringType
    {
        return $this->call(new GetField(TypeEnum::String, $field));
    }

    public function getBoolean(string $field): BooleanType
    {
        return $this->call(new GetField(TypeEnum::Boolean, $field));
    }

    public function getObject(string $field): ObjectType
    {
        return $this->call(new GetField(TypeEnum::Object, $field));
    }
}

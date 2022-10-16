<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\TypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Numeric\GetField;

abstract class AbstractDataType extends AbstractType implements CountableTypeInterface
{
    /** @return NumericType|AngleType|StringType|BooleanType|ObjectType */
    protected function call(AbstractFunction $function): TypeInterface
    {
        return parent::call($function);
    }

    public function getNumeric(string $field): NumericType
    {
        return $this->call(new GetField(TypeEnum::Numeric, $field));
    }

    public function getAngle(
        string $field,
        AngleTypeEnum $type = AngleTypeEnum::Degrees,
    ): AngleType {
        return $this->call(new GetField(match ($type) {
            AngleTypeEnum::Degrees => TypeEnum::Degrees,
            AngleTypeEnum::Radians => TypeEnum::Radians,
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
}

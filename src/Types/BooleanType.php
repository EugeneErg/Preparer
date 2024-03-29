<?php

declare(strict_types=1);

namespace EugeneErg\Preparer\Types;

use EugeneErg\Preparer\Enums\AngleTypeEnum;
use EugeneErg\Preparer\Enums\NumericTypeEnum;
use EugeneErg\Preparer\Functions\AbstractFunction;
use EugeneErg\Preparer\Functions\Angle\ToAngle;
use EugeneErg\Preparer\Functions\Boolean\AndFunction;
use EugeneErg\Preparer\Functions\Numeric\ToNumeric;
use EugeneErg\Preparer\Functions\String\ToString;

class BooleanType extends AbstractFieldType implements FieldTypeInterface
{
    /** @return NumericType|AngleType|StringType|BooleanType */
    protected function call(AbstractFunction $function): AbstractType
    {
        return parent::call($function);
    }

    public function toNumeric(
        NumericTypeEnum $type = NumericTypeEnum::Integer,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): NumericType {
        return $this->call(new ToNumeric($type, $digitsCount, $accuracy));
    }

    public function toAngle(
        AngleTypeEnum $type,
        NumericTypeEnum $numericType = NumericTypeEnum::Integer,
        ?int $digitsCount = null,
        ?int $accuracy = null,
    ): AngleType {
        return $this->call(new ToAngle($this, $type, $numericType, $digitsCount, $accuracy));
    }

    public function toString(): StringType
    {
        return $this->call(new ToString());
    }

    public function and(BooleanType $value): self
    {
        return $this->call(new AndFunction($this, $value));
    }
}
